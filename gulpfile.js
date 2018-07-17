const path = require('path'),
    gchmod = require('gulp-chmod'),
    CWD = process.cwd()
    gconcat = require('gulp-concat'),
    grename = require('gulp-rename'),
    _ = require('underscore'),
    gutil = require('gulp-util'),
    grename = require('gulp-rename'),
    del = require('del'),
    exec = require('child_process').exec,
    execFile = require('child_process').execFile,
    fs = require('fs'),
    mkdirp = require('mkdirp'),
    replace = require('gulp-replace'),
    isWIN = /^win/.test(process.platform);

var gulp = require('gulp'),
    config = require('./config/build.json');

function getVariableName(varName) {
    if (/^\%.+\%$/.test(varName)) {
        return varName.match(/^\%(.+)\%$/)[1];
    }
    return null;
}

function getValue(key, vars) {
    var value = this[key],
        finalValue = '',
        variable,
        i;

    if (typeof value !== 'string') {
        return value;
    }

    if (/^\$\[(.*)\]\$$/.test(value)) {
        value = JSON.parse(value.match(/^\$(\[.*\])\$$/)[1]);

        for (i = 0; i < value.length; i += 1) {
            if (variable = getVariableName(value[i])) {
                finalValue += vars[variable];
            } else {
                finalValue += value[i];
            }
        }
    } else if (variable = getVariableName(value)) {
         finalValue = vars[variable];
    } else {
        finalValue = value;
    }
    
    return finalValue;
}

function executeSequence(sequence, callback) {
    var current;
    if (sequence.length) {
        current = sequence.shift();
        current(function (err, returnValue) {
            if (err) {
                callback(err);
                return;
            }
            executeSequence(sequence, callback);
        });
    } else {
        callback();
    }
}

function validatePath(dir) {
    // for the sake of security absolute paths are not allowed
    if (path.isAbsolute(dir)) {
        throw new Error('Absolute paths are not allowed (used path: ' + dir + ').');
    }

    // for the sake of security paths directioning outside the current directory are not allowed
    if (/\.\./.test(path.relative(CWD, dir))) {
        throw new Error('Paths poiting outside ProcessMaker project are not allowed (used path: ' + dir + ').');
    }
}

function cleanDirectory(dir, callback) {
    validatePath(dir);
    del.sync([
        dir
    ], {force: true});

    return mkdirp(dir, function (err) {
        if (typeof callback === 'function') {
            callback(err);
        }
    });
}

function executeRake(cb, vars) {
    var task = this.rake_task.trim(),
        dir = this.rake_dir.trim(),
        that = this;

    validatePath(dir);

    gutil.log(gutil.colors.yellow('Executing Rake ' + (task ? '(task: ' + task + ') ' : '') + 'in ' + this.rake_dir));
    exec('rake ' + task, {
        cwd: path.join(__dirname , dir)
    }, function (err, stdout, stderr) {
        if (err) {
            cb(err);
        } else {
            if (typeof that.variable === 'string') {
                vars[that.variable] = stdout.trim();
            }
            cb();
        }
    });
}

function executeCopy(cb) {
    var src_dir = this['src_dir'] || '',
        dest_dir = this['dest_dir'] || '',
        file_mode = this['file_mode'],
        stream,
        file,
        file_dest_dir,
        file_dest_name,
        files_length = this['files'].length,
        origin,
        destination,
        i,
        copied = 0,
        finishCopy = function () {
            copied ++;
            if (copied === files_length) {
                cb();
            }
        },
        showError = function (e) {
            gutil.log(gutil.colors.red(e));
        };

    gutil.log(gutil.colors.yellow('Executing copy of files from ' + src_dir + ' to ' + dest_dir));

    for (i = 0; i < this['files'].length; i += 1) {
        file = this.files[i];
        file_dest_dir = file['dest_dir'] || '';
        file_dest_name = file['dest_name'] || '';

        origin = path.join(src_dir, file['src']); 
        destination = path.join(dest_dir, file_dest_dir);

        validatePath(destination);

        gutil.log('copying: ' + origin + ' to ' + path.join(destination, file_dest_name));

        if (file_dest_name) {
            stream = gulp.src([origin])
                .pipe(grename(file_dest_name))
                .pipe(gulp.dest(destination))
                .on('error', showError)
                .on('end', finishCopy); 
        } else {
            stream = gulp.src([origin])
                .pipe(gulp.dest(destination))
                .on('error', showError)
                .on('end', finishCopy); 
        }

        if (file_mode) {
            stream.pipe(gchmod(file_mode));
        }
    }
}

function executeDir(cb) {
    gutil.log(gutil.colors.yellow('Executing directory operation (' + this.operation + ') over ' + this.dir));
    validatePath(this.dir);
    switch (this.operation) {
        case 'clean':
            cleanDirectory(this.dir, cb);
    }
}

function executeRead(cb, vars) {
    var that = this,
        src = getValue.call(this, 'src', vars);
    gutil.log(gutil.colors.yellow('Executing read operation from ' + src));

    validatePath(src);

    fs.readFile(src, function (err, data) {
        if (err) {
            return cb(err);
        }

        data = data.toString();

        if (typeof that.variable === 'string') {
            vars[that.variable] = data;
        }
        cb();
    });
}

function executeParse(cb, vars) {
    var data, path, i;

    gutil.log(gutil.colors.yellow('Executing parsing (' + this.parseTo + ')'));

    switch (this.parseTo) {
        case 'json':
            path = this.path || "";
            path = path.split("/");
            data = JSON.parse(getValue.call(this, 'data', vars));

            for (i = 0; i < path.length; i += 1) {
                if (path[i]) {
                    data = data[path[i]];
                }
            }
            break;
        default:
            break;
    }

    if (typeof this.variable === 'string') {
        vars[this.variable] = data;
    }
    cb();
}

function executeReplaceFileContents(cb, vars) {
    var originalFile = getValue.call(this, 'orig_file', vars), // It can be string or array
        replacements = getValue.call(this, 'replacements', vars),
        dest_dir = getValue.call(this, 'dest_dir', vars),
        dest_file = getValue.call(this, 'dest_file', vars),
        completePath = path.join(dest_dir, dest_file),
        that = this,
        replacement,
        stream,
        i;

    gutil.log(gutil.colors.yellow('Executing content replacement of file ' + originalFile + ' files to ' + completePath));

    if (!_.isArray(replacements)) {
        return cb("The replacements must be an array.");
    }

    validatePath(completePath);

    stream = gulp.src(originalFile);

    for (i = 0; i < replacements.length; i += 1) {
        replacement = replacements[i];
        stream = stream.pipe(replace(
                getValue.call(replacement, 'search', vars),
                getValue.call(replacement, 'replaceBy', vars)
            ));
    }

    stream.pipe(grename(dest_file))
        .pipe(gulp.dest(dest_dir))
        .on('end', function () {
            if (typeof this.variable === 'string') {
                vars[this.variable] = data;
            }
            cb();
        });  
}

function executeReplaceString(cb, vars) {
    var target = getValue.call(this, 'target', vars),
        replacement,
        result = [],
        isString = false,
        i, 
        j;

    gutil.log(gutil.colors.yellow('Executing replacing'));

    if (typeof target === 'string') {
        target = [target];
        isString = true;
    }
    
    for (j = 0; j < target.length; j += 1) {
        result[j] = target[j];
        for (i = 0; i < this.replacements.length; i += 1) {
            replacement = this.replacements[i];
            result[j] = result[j].replace(getValue.call(replacement, "search", vars), getValue.call(replacement, "replaceBy", vars));
        }   
    }
    
    result = isString ? result[0] : result;
    
    if (typeof this.variable === 'string') {
        vars[this.variable] = result;
    }
    cb();
}

function executeConcatenate(cb, vars) {
    var files = getValue.call(this, 'files', vars),
        name = getValue.call(this, 'dest_name', vars),
        completePath,
        result = "",
        that = this;

    if (this.dest_dir && this.dest_name) {
        completePath = path.join(this.dest_dir, name);
        gutil.log(gutil.colors.yellow('Executing concatenation of ' + files.length + ' files to ' + completePath));
        validatePath(completePath);
        gulp.src(files)
            .on('error', function (err) {
                cb(err);
            })
            .on('data', function (chunk) {
                result += chunk.contents;
            })
            .on('end', function () {
                if (typeof that.variable === 'string') {
                    vars[that.variable] = result;
                }
                cb();
            })
            .pipe(gconcat(name))
            .pipe(gulp.dest(this['dest_dir']));
    } else {
        gutil.log(gutil.colors.yellow('Executing concatenation of ' + files.length + ' files'));
        
        gulp.src(files)
            .pipe(gconcat('__TMP__'))
            .on('error', function (err) {
                cb(err);
            })
            .on('data', function (chunk) {
                result += chunk.contents;
            })
            .on('end', function () {
                if (typeof that.variable === 'string') {
                    vars[that.variable] = result;
                }
                cb();
            });
    }
}

function executeWrite(cb, vars) {
    var thePath = path.join(
            getValue.call(this, "dest_dir", vars),
            getValue.call(this, "dest_name", vars)
        );

    gutil.log(gutil.colors.yellow('Executing writing of file ' + thePath));

    validatePath(thePath);

    fs.writeFile(thePath, getValue.call(this, "contents", vars), function (err) {
        cb(err);
    });
}

function executeExec(cb, vars) {
    var that = this,
        working_dir = path.join(this['working_dir']), // This modifies the path to POSIX/Windows
        args = this['arguments'] || [],
        opts = {}; 

    if (isWIN) {
        opts['shell'] = 'git-bash.exe';
    }

    gutil.log(gutil.colors.yellow('Executing shell command: ') + gutil.colors.bold.yellow(this['command'] + ' ' + args.join(' ')) + ' in ' + gutil.colors.bold.yellow(working_dir));
    validatePath(working_dir);

    execFile(this['command'], args, {
        cwd: working_dir
    }, function (err, stdout, stderr) {
        if (err) {
            return cb(err);
        }
        if (typeof that.variable === 'string') {
            vars[that.variable] = stdout.trim();
        }
        cb();
    });
}

function executeVariable(cb, vars) {
    var varName = getValue.call(this, 'name', vars);
    gutil.log(gutil.colors.yellow('Executing assignation into variable ' + varName));
    vars[varName] = getValue.call(this, 'value', vars);
    cb();
}

function processTask(callback, stepIndex, variables) {
    var step, fn, i, that = this;

    stepIndex = stepIndex || 0;
    variables = variables || {};

    if (stepIndex === 0) {
        gutil.log(gutil.colors.green(this.description));
    }

    try {
        if (stepIndex < this.steps.length) {
            step = this.steps[stepIndex];

            if (step) {
                switch (step.type) {
                    case 'rake':
                        fn = executeRake;
                        break;
                    case 'copy':
                        fn = executeCopy;
                        break;
                    case 'dir':
                        fn = executeDir;
                        break;
                    case 'read':
                        fn = executeRead;
                        break;
                    case 'parse':
                        fn = executeParse;
                        break;
                    case 'replaceFileContents':
                        fn = executeReplaceFileContents;
                        break;
                    case 'replace_string':
                        fn = executeReplaceString;
                        break;
                    case 'concatenate':
                        fn = executeConcatenate;
                        break;
                    case 'write':
                        fn = executeWrite;
                        break;
                    case 'exec':
                        fn = executeExec;
                        break;
                    case 'variable':
                        fn = executeVariable;
                }

                if (fn) {
                    fn.call(step, function (err) {
                        if (err) {
                            callback(err);
                            return;
                        }
                        processTask.call(that, callback, stepIndex + 1, variables);
                    }, variables);
                } else {
                    processTask.call(this, callback, stepIndex + 1, variables);
                }
            }
        } else {
            gutil.log(gutil.colors.green('DONE!'));
            callback();
        } 
    } catch (e) {
        gutil.log(gutil.colors.red('Error at processing ' + this.id + ', step #' + (stepIndex + 1) + ' (' + step.type + '): ' + e.message));
        process.exit();
    }
}

gulp.task('clean', function () {
    gutil.log(gutil.colors.green('Cleaning directories...'));

    cleanDirectory('workflow/public_html/lib');
});

/**
 * This scheduled task is to be able to create the guest user constants
 */
gulp.task('__env', function (cb) {
    var data = require('./config/enviromentvariables.json'),
        pathEnviroment = 'vendor/colosa/MichelangeloFE/src/enviroment/',
        content = 'var __env = __env || {};';

    gutil.log(gutil.colors.green('Creating System Constants...'));
    if (!fs.existsSync(pathEnviroment)){
        fs.mkdirSync(pathEnviroment);
    }
    fs.writeFile(
        pathEnviroment + 'constants.js',
        content + '__env.USER_GUEST = ' + JSON.stringify(data.constants.userguest) + ';',
        cb
    );
});

gulp.task('default', ['clean', '__env'], function (cb) {
    var i, tasks = [];

    gutil.log(gutil.colors.green('Initializing ProcessMaker building...'));

    for (i = 0; i < config.length; i += 1) {
        tasks.push(_.bind(processTask, config[i]));
    }
    executeSequence(tasks, cb);
});