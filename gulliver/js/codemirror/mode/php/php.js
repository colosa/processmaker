(function() {
  function keywords(str) {
    var obj = {}, words = str.split(" ");
    for (var i = 0; i < words.length; ++i) obj[words[i]] = true;
    return obj;
  }
  function heredoc(delim) {
    return function(stream, state) {
      if (stream.match(delim)) state.tokenize = null;
      else stream.skipToEnd();
      return "string";
    };
  }
  
  var PM_keywords = function(){
    function PMkey() {return {style: "PMbuiltin"};}
  var PMfunctions = {
      "formatDate":PMkey(), "CurrentDate":PMkey(), "CurrentTime":PMkey(),
      "literalDate":PMkey(), "capitalize":PMkey(), "lowerCase":PMkey(),
      "upperCase":PMkey(), "userInfo":PMkey(), "executeQuery":PMkey(),
      "orderGrid":PMkey(), "evaluateFunction":PMkey(), "PMFTaskCase":PMkey(),
      "PMFTaskList":PMkey(), "PMFUserList":PMkey(), "PMFGroupList":PMkey(),
      "PMFRoleList":PMkey(), "PMFCaseList":PMkey(), "PMFProcessList":PMkey(),
      "PMFSendVariables":PMkey(), "PMFDerivateCase":PMkey(), "PMFNewCaseImpersonate":PMkey(),
      "PMFNewCase":PMkey(), "PMFPauseCase":PMkey(), "PMFUnpauseCase":PMkey(),
      "PMFAssignUserToGroup":PMkey(), "PMFCreateUser":PMkey(), "PMFUpdateUser":PMkey(),
      "PMFInformationUser":PMkey(), "generateCode":PMkey(), "setCaseTrackerCode":PMkey(),
      "jumping":PMkey(), "PMFRedirectToStep":PMkey(), "pauseCase":PMkey(),
      "PMFSendMessage":PMkey(), "PMFgetLabelOption":PMkey(), "PMFGenerateOutputDocument":PMkey(),
      "PMFGetUserEmailAddress":PMkey(), "PMFGetNextAssignedUser":PMkey(), "PMFDeleteCase":PMkey(),
      "PMFCancelCase":PMkey(), "PMFAddInputDocument":PMkey(), "PMFAddCaseNote":PMkey(),
      "PMFGetCaseNotes":PMkey()};
    return PMfunctions;
  }();
  
    var php_keywords = function(){
    function phpbuild() {return {style: "builtin"};}
    function phpatom() {return {style: "atom"};}
    var Phpfunctions = {
      "func_num_args":phpbuild(),"func_get_arg":phpbuild(),"func_get_args":phpbuild(),"strlen":phpbuild(),"strcmp":phpbuild(),
      "strncmp":phpbuild(),"strcasecmp":phpbuild(),"strncasecmp":phpbuild(),"each error_reporting":phpbuild(),"define":phpbuild(),
      "defined":phpbuild(),"trigger_error":phpbuild(),"user_error":phpbuild(),"set_error_handler":phpbuild(),"restore_error_handler":phpbuild(),
      "get_declared_classes":phpbuild(),"get_loaded_extensions":phpbuild(),"extension_loaded":phpbuild(),"get_extension_funcs":phpbuild(),
      "debug_backtrace":phpbuild(),"constant":phpbuild(),"bin2hex":phpbuild(),"sleep":phpbuild(),"usleep":phpbuild(),"time":phpbuild(),
      "mktime":phpbuild(),"gmmktime":phpbuild(),"strftime":phpbuild(),"gmstrftime":phpbuild(),"strtotime":phpbuild(),"date":phpbuild(),
      "gmdate":phpbuild(),"getdate":phpbuild(),"localtime":phpbuild(),"checkdate":phpbuild(),"flush":phpbuild(),"wordwrap":phpbuild(),
      "htmlspecialchars":phpbuild(),"htmlentities":phpbuild(),"html_entity_decode":phpbuild(),"md5":phpbuild(),"md5_file":phpbuild(),
      "crc32 getimagesize":phpbuild(),"image_type_to_mime_type":phpbuild(),"phpinfo":phpbuild(),"phpversion":phpbuild(),
      "phpcredits":phpbuild(),"strnatcmp":phpbuild(),"strnatcasecmp":phpbuild(),"substr_count":phpbuild(),"strspn":phpbuild(),
      "strcspn":phpbuild(),"strtok":phpbuild(),"strtoupper":phpbuild(),"strtolower":phpbuild(),"strpos":phpbuild(),
      "strrpos":phpbuild(),"strrev":phpbuild(),"hebrev":phpbuild(),"hebrevc":phpbuild(),"nl2br":phpbuild(),"basename":phpbuild(),
      "dirname":phpbuild(),"pathinfo":phpbuild(),"stripslashes":phpbuild(),"stripcslashes":phpbuild(),"strstr":phpbuild(),
      "stristr":phpbuild(),"strrchr":phpbuild(),"str_shuffle":phpbuild(),"str_word_count":phpbuild(),"strcoll":phpbuild(),
      "substr":phpbuild(),"substr_replace":phpbuild(),"quotemeta":phpbuild(),"ucfirst":phpbuild(),"ucwords":phpbuild(),
      "strtr":phpbuild(),"addslashes":phpbuild(),"addcslashes":phpbuild(),"rtrim":phpbuild(),"str_replace":phpbuild(),
      "str_repeat":phpbuild(),"count_chars":phpbuild(),"chunk_split":phpbuild(),"trim":phpbuild(),"ltrim":phpbuild(),
      "strip_tags":phpbuild(),"similar_text":phpbuild(),"explode":phpbuild(),"implode":phpbuild(),"setlocale":phpbuild(),
      "localeconv":phpbuild(),"parse_str":phpbuild(),"str_pad":phpbuild(),"chop":phpbuild(),"strchr":phpbuild(),"sprintf":phpbuild(),
      "printf":phpbuild(),"vprintf":phpbuild(),"vsprintf":phpbuild(),"sscanf":phpbuild(),"fscanf":phpbuild(),"parse_url":phpbuild(),
      "urlencode":phpbuild(),"urldecode":phpbuild(),"rawurlencode":phpbuild(),"rawurldecode":phpbuild(),"readlink":phpbuild(),
      "linkinfo":phpbuild(),"link":phpbuild(),"unlink":phpbuild(),"exec":phpbuild(),"system":phpbuild(),"escapeshellcmd":phpbuild(),
      "escapeshellarg":phpbuild(),"passthru":phpbuild(),"shell_exec":phpbuild(),"proc_open":phpbuild(),"proc_close":phpbuild(),
      "rand":phpbuild(),"srand":phpbuild(),"getrandmax":phpbuild(),"mt_rand":phpbuild(),"mt_srand":phpbuild(),
      "mt_getrandmax":phpbuild(),"base64_decode":phpbuild(),"base64_encode":phpbuild(),"abs":phpbuild(),"ceil":phpbuild(),
      "floor":phpbuild(),"round":phpbuild(),"is_finite":phpbuild(),"is_nan":phpbuild(),"is_infinite":phpbuild(),"bindec":phpbuild(),
      "hexdec":phpbuild(),"octdec":phpbuild(),"decbin":phpbuild(),"decoct":phpbuild(),"dechex":phpbuild(),"base_convert":phpbuild(),
      "number_format":phpbuild(),"fmod":phpbuild(),"ip2long":phpbuild(),"long2ip":phpbuild(),"getenv":phpbuild(),"putenv":phpbuild(),
      "getopt":phpbuild(),"microtime":phpbuild(),"gettimeofday":phpbuild(),"getrusage":phpbuild(),"uniqid":phpbuild(),
      "quoted_printable_decode":phpbuild(),"set_time_limit":phpbuild(),"get_cfg_var":phpbuild(),"magic_quotes_runtime":phpbuild(),
      "set_magic_quotes_runtime":phpbuild(),"get_magic_quotes_gpc":phpbuild(),"get_magic_quotes_runtime":phpbuild(),
      "import_request_variables":phpbuild(),"error_log":phpbuild(),"serialize":phpbuild(),"unserialize":phpbuild(),
      "memory_get_usage":phpbuild(),"var_dump":phpbuild(),"var_export":phpbuild(),"debug_zval_dump":phpbuild(),"print_r":phpbuild(),
      "highlight_file":phpbuild(),"show_source":phpbuild(),"highlight_string":phpbuild(),"ini_get":phpbuild(),"ini_get_all":phpbuild(),
      "ini_set":phpbuild(),"ini_alter":phpbuild(),"ini_restore":phpbuild(),"get_include_path":phpbuild(),"set_include_path":phpbuild(),
      "restore_include_path":phpbuild(),"setcookie":phpbuild(),"header":phpbuild(),"headers_sent":phpbuild(),
      "connection_aborted":phpbuild(),"connection_status":phpbuild(),"ignore_user_abort":phpbuild(),"parse_ini_file":phpbuild(),
      "is_uploaded_file":phpbuild(),"move_uploaded_file":phpbuild(),"intval":phpbuild(),"floatval":phpbuild(),"doubleval":phpbuild(),
      "strval":phpbuild(),"gettype":phpbuild(),"settype":phpbuild(),"is_null":phpbuild(),"is_resource":phpbuild(),"is_bool":phpbuild(),
      "is_long":phpbuild(),"is_float":phpbuild(),"is_int":phpbuild(),"is_integer":phpbuild(),"is_double":phpbuild(),
      "is_real":phpbuild(),"is_numeric":phpbuild(),"is_string":phpbuild(),"is_array":phpbuild(),"is_object":phpbuild(),
      "is_scalar":phpbuild(),"ereg":phpbuild(),"ereg_replace":phpbuild(),"eregi":phpbuild(),"eregi_replace":phpbuild(),
      "split":phpbuild(),"spliti":phpbuild(),"join":phpbuild(),"sql_regcase":phpbuild(),"dl":phpbuild(),"pclose":phpbuild(),
      "popen":phpbuild(),"readfile":phpbuild(),"rewind":phpbuild(),"rmdir":phpbuild(),"umask":phpbuild(),"fclose":phpbuild(),
      "feof":phpbuild(),"fgetc":phpbuild(),"fgets":phpbuild(),"fgetss":phpbuild(),"fread":phpbuild(),"fopen":phpbuild(),
      "fpassthru":phpbuild(),"ftruncate":phpbuild(),"fstat":phpbuild(),"fseek":phpbuild(),"ftell":phpbuild(),"fflush":phpbuild(),
      "fwrite":phpbuild(),"fputs":phpbuild(),"mkdir":phpbuild(),"rename":phpbuild(),"copy":phpbuild(),"tempnam":phpbuild(),
      "tmpfile":phpbuild(),"file":phpbuild(),"file_get_contents":phpbuild(),"stream_select":phpbuild(),
      "stream_context_create":phpbuild(),"stream_context_set_params":phpbuild(),"stream_context_set_option":phpbuild(),
      "stream_context_get_options":phpbuild(),"stream_filter_prepend":phpbuild(),"stream_filter_append":phpbuild(),
      "fgetcsv":phpbuild(),"flock":phpbuild(),"get_meta_tags":phpbuild(),"stream_set_write_buffer":phpbuild(),
      "set_file_buffer":phpbuild(),"set_socket_blocking":phpbuild(),"stream_set_blocking":phpbuild(),"socket_set_blocking":phpbuild(),
      "stream_get_meta_data":phpbuild(),"stream_register_wrapper":phpbuild(),"stream_wrapper_register":phpbuild(),
      "stream_set_timeout":phpbuild(),"socket_set_timeout":phpbuild(),"socket_get_status":phpbuild(),"realpath":phpbuild(),
      "fnmatch":phpbuild(),"fsockopen":phpbuild(),"pfsockopen":phpbuild(),"pack":phpbuild(),"unpack":phpbuild(),
      "get_browser":phpbuild(),"crypt":phpbuild(),"opendir":phpbuild(),"closedir":phpbuild(),"chdir":phpbuild(),
      "getcwd":phpbuild(),"rewinddir":phpbuild(),"readdir":phpbuild(),"dir":phpbuild(),"glob":phpbuild(),"fileatime":phpbuild(),
      "filectime":phpbuild(),"filegroup":phpbuild(),"fileinode":phpbuild(),"filemtime":phpbuild(),"fileowner":phpbuild(),
      "fileperms":phpbuild(),"filesize":phpbuild(),"filetype":phpbuild(),"file_exists":phpbuild(),"is_writable":phpbuild(),
      "is_writeable":phpbuild(),"is_readable":phpbuild(),"is_executable":phpbuild(),"is_file":phpbuild(),"is_dir":phpbuild(),
      "is_link":phpbuild(),"stat":phpbuild(),"lstat":phpbuild(),"chown":phpbuild(),"touch":phpbuild(),"clearstatcache":phpbuild(),
      "mail":phpbuild(),"ob_start":phpbuild(),"ob_flush":phpbuild(),"ob_clean":phpbuild(),"ob_end_flush":phpbuild(),
      "ob_end_clean":phpbuild(),"ob_get_flush":phpbuild(),"ob_get_clean":phpbuild(),"ob_get_length":phpbuild(),
      "ob_get_level":phpbuild(),"ob_get_status":phpbuild(),"ob_get_contents":phpbuild(),"ob_implicit_flush":phpbuild(),
      "ob_list_handlers":phpbuild(),"ksort":phpbuild(),"krsort":phpbuild(),"natsort":phpbuild(),"natcasesort":phpbuild(),
      "asort":phpbuild(),"arsort":phpbuild(),"sort":phpbuild(),"rsort":phpbuild(),"usort":phpbuild(),"uasort":phpbuild(),
      "uksort":phpbuild(),"shuffle":phpbuild(),"array_walk":phpbuild(),"count":phpbuild(),"end":phpbuild(),"prev":phpbuild(),
      "next":phpbuild(),"reset":phpbuild(),"current":phpbuild(),"key":phpbuild(),"min":phpbuild(),"max":phpbuild(),
      "in_array":phpbuild(),"array_search":phpbuild(),"extract":phpbuild(),"compact":phpbuild(),"array_fill":phpbuild(),
      "range":phpbuild(),"array_multisort":phpbuild(),"array_push":phpbuild(),"array_pop":phpbuild(),"array_shift":phpbuild(),
      "array_unshift":phpbuild(),"array_splice":phpbuild(),"array_slice":phpbuild(),"array_merge":phpbuild(),
      "array_merge_recursive":phpbuild(),"array_keys":phpbuild(),"array_values":phpbuild(),"array_count_values":phpbuild(),
      "array_reverse":phpbuild(),"array_reduce":phpbuild(),"array_pad":phpbuild(),"array_flip":phpbuild(),
      "array_change_key_case":phpbuild(),"array_rand":phpbuild(),"array_unique":phpbuild(),"array_intersect":phpbuild(),
      "array_intersect_assoc":phpbuild(),"array_diff":phpbuild(),"array_diff_assoc":phpbuild(),"array_sum":phpbuild(),
      "array_filter":phpbuild(),"array_map":phpbuild(),"array_chunk":phpbuild(),"array_key_exists":phpbuild(),"pos":phpbuild(),
      "sizeof":phpbuild(),"key_exists":phpbuild(),"assert":phpbuild(),"assert_options":phpbuild(),"version_compare":phpbuild(),
      "ftok":phpbuild(),"str_rot13":phpbuild(),"aggregate":phpbuild(),"session_name":phpbuild(),"session_module_name":phpbuild(),
      "session_save_path":phpbuild(),"session_id":phpbuild(),"session_regenerate_id":phpbuild(),"session_decode":phpbuild(),
      "session_register":phpbuild(),"session_unregister":phpbuild(),"session_is_registered":phpbuild(),"session_encode":phpbuild(),
      "session_start":phpbuild(),"session_destroy":phpbuild(),"session_unset":phpbuild(),"session_set_save_handler":phpbuild(),
      "session_cache_limiter":phpbuild(),"session_cache_expire":phpbuild(),"session_set_cookie_params":phpbuild(),
      "session_get_cookie_params":phpbuild(),"session_write_close":phpbuild(),"preg_match":phpbuild(),"preg_match_all":phpbuild(),
      "preg_replace":phpbuild(),"preg_replace_callback":phpbuild(),"preg_split":phpbuild(),"preg_quote":phpbuild(),
      "preg_grep":phpbuild(),"overload":phpbuild(),"ctype_alnum":phpbuild(),"ctype_alpha":phpbuild(),"ctype_cntrl":phpbuild(),
      "ctype_digit":phpbuild(),"ctype_lower":phpbuild(),"ctype_graph":phpbuild(),"ctype_print":phpbuild(),"ctype_punct":phpbuild(),
      "ctype_space":phpbuild(),"ctype_upper":phpbuild(),"ctype_xdigit":phpbuild(),"virtual":phpbuild(),
      "apache_request_headers":phpbuild(),"apache_note":phpbuild(),"apache_lookup_uri":phpbuild(),"apache_child_terminate":phpbuild(),
      "apache_setenv":phpbuild(),"apache_response_headers":phpbuild(),"apache_get_version":phpbuild(),
      "getallheaders":phpbuild(),"mysql_connect":phpbuild(),"mysql_pconnect":phpbuild(),"mysql_close":phpbuild(),
      "mysql_select_db":phpbuild(),"mysql_create_db":phpbuild(),"mysql_drop_db":phpbuild(),"mysql_query":phpbuild(),
      "mysql_unbuffered_query":phpbuild(),"mysql_db_query":phpbuild(),"mysql_list_dbs":phpbuild(),"mysql_list_tables":phpbuild(),
      "mysql_list_fields":phpbuild(),"mysql_list_processes":phpbuild(),"mysql_error":phpbuild(),"mysql_errno":phpbuild(),
      "mysql_affected_rows":phpbuild(),"mysql_insert_id":phpbuild(),"mysql_result":phpbuild(),"mysql_num_rows":phpbuild(),
      "mysql_num_fields":phpbuild(),"mysql_fetch_row":phpbuild(),"mysql_fetch_array":phpbuild(),"mysql_fetch_assoc":phpbuild(),
      "mysql_fetch_object":phpbuild(),"mysql_data_seek":phpbuild(),"mysql_fetch_lengths":phpbuild(),"mysql_fetch_field":phpbuild(),
      "mysql_field_seek":phpbuild(),"mysql_free_result":phpbuild(),"mysql_field_name":phpbuild(),"mysql_field_table":phpbuild(),
      "mysql_field_len":phpbuild(),"mysql_field_type":phpbuild(),"mysql_field_flags":phpbuild(),"mysql_escape_string":phpbuild(),
      "mysql_real_escape_string":phpbuild(),"mysql_stat":phpbuild(),"mysql_thread_id":phpbuild(),"mysql_client_encoding":phpbuild(),
      "mysql_get_client_info":phpbuild(),"mysql_get_host_info":phpbuild(),"mysql_get_proto_info":phpbuild(),
      "mysql_get_server_info":phpbuild(),"mysql_info":phpbuild(),"mysql":phpbuild(),"mysql_fieldname":phpbuild(),
      "mysql_fieldtable":phpbuild(),"mysql_fieldlen":phpbuild(),"mysql_fieldtype":phpbuild(),"mysql_fieldflags":phpbuild(),
      "mysql_selectdb":phpbuild(),"mysql_createdb":phpbuild(),"mysql_dropdb":phpbuild(),"mysql_freeresult":phpbuild(),
      "mysql_numfields":phpbuild(),"mysql_numrows":phpbuild(),"mysql_listdbs":phpbuild(),"mysql_listtables":phpbuild(),
      "mysql_listfields":phpbuild(),"mysql_db_name":phpbuild(),"mysql_dbname":phpbuild(),"mysql_tablename":phpbuild(),
      "mysql_table_name":phpbuild(),"pg_connect":phpbuild(),"pg_pconnect":phpbuild(),"pg_close":phpbuild(),
      "pg_connection_status":phpbuild(),"pg_connection_busy":phpbuild(),"pg_connection_reset":phpbuild(),"pg_host":phpbuild(),
      "pg_dbname":phpbuild(),"pg_port":phpbuild(),"pg_tty":phpbuild(),"pg_options":phpbuild(),"pg_ping":phpbuild(),
      "pg_query":phpbuild(),"pg_send_query":phpbuild(),"pg_cancel_query":phpbuild(),"pg_fetch_result":phpbuild(),
      "pg_fetch_row":phpbuild(),"pg_fetch_assoc":phpbuild(),"pg_fetch_array":phpbuild(),"pg_fetch_object":phpbuild(),
      "pg_fetch_all":phpbuild(),"pg_affected_rows":phpbuild(),"pg_get_result":phpbuild(),"pg_result_seek":phpbuild(),
      "pg_result_status":phpbuild(),"pg_free_result":phpbuild(),"pg_last_oid":phpbuild(),"pg_num_rows":phpbuild(),
      "pg_num_fields":phpbuild(),"pg_field_name":phpbuild(),"pg_field_num":phpbuild(),"pg_field_size":phpbuild(),
      "pg_field_type":phpbuild(),"pg_field_prtlen":phpbuild(),"pg_field_is_null":phpbuild(),"pg_get_notify":phpbuild(),
      "pg_get_pid":phpbuild(),"pg_result_error":phpbuild(),"pg_last_error":phpbuild(),"pg_last_notice":phpbuild(),
      "pg_put_line":phpbuild(),"pg_end_copy":phpbuild(),"pg_copy_to":phpbuild(),"pg_copy_from":phpbuild(),"pg_trace":phpbuild(),
      "pg_untrace":phpbuild(),"pg_lo_create":phpbuild(),"pg_lo_unlink":phpbuild(),"pg_lo_open":phpbuild(),"pg_lo_close":phpbuild(),
      "pg_lo_read":phpbuild(),"pg_lo_write":phpbuild(),"pg_lo_read_all":phpbuild(),"pg_lo_import":phpbuild(),"pg_lo_export":phpbuild(),
      "pg_lo_seek":phpbuild(),"pg_lo_tell":phpbuild(),"pg_escape_string":phpbuild(),"pg_escape_bytea":phpbuild(),
      "pg_unescape_bytea":phpbuild(),"pg_client_encoding":phpbuild(),"pg_set_client_encoding":phpbuild(),"pg_meta_data":phpbuild(),
      "pg_convert":phpbuild(),"pg_insert":phpbuild(),"pg_update":phpbuild(),"pg_delete":phpbuild(),"pg_select":phpbuild(),
      "pg_exec":phpbuild(),"pg_getlastoid":phpbuild(),"pg_cmdtuples":phpbuild(),"pg_errormessage":phpbuild(),"pg_numrows":phpbuild(),
      "pg_numfields":phpbuild(),"pg_fieldname":phpbuild(),"pg_fieldsize":phpbuild(),"pg_fieldtype":phpbuild(),"pg_fieldnum":phpbuild(),
      "pg_fieldprtlen":phpbuild(),"pg_fieldisnull":phpbuild(),"pg_freeresult":phpbuild(),"pg_result":phpbuild(),
      "pg_loreadall":phpbuild(),"pg_locreate":phpbuild(),"pg_lounlink":phpbuild(),"pg_loopen":phpbuild(),"pg_loclose":phpbuild(),
      "pg_loread":phpbuild(),"pg_lowrite":phpbuild(),"pg_loimport":phpbuild(),"pg_loexport":phpbuild(),"echo":phpbuild(),
      "print":phpbuild(),"global":phpbuild(),"static":phpbuild(),"exit":phpbuild(),"array":phpbuild(),"empty":phpbuild(),
      "eval":phpbuild(),"isset":phpbuild(),"unset":phpbuild(),"die":phpbuild(),"include":phpbuild(),"require":phpbuild(),
      "include_once":phpbuild(),"require_once":phpbuild(),
      "true":phpatom(),"false":phpatom(),"null":phpatom(),"TRUE":phpatom(),"FALSE":phpatom(),"NULL":phpatom(),
      "__CLASS__":phpatom(),"__DIR__":phpatom(),"__FILE__":phpatom(),"__LINE__":phpatom(),"__METHOD__":phpatom(),
      "__FUNCTION__":phpatom(),"__NAMESPACE__":phpatom()};
    return Phpfunctions;
  }();
  
  
  var phpConfig = {
    name: "clike",
    keywords: keywords("abstract and array as break case catch class clone const continue declare default " +
                       "do else elseif enddeclare endfor endforeach endif endswitch endwhile extends final " +
                       "for foreach function global goto if implements interface instanceof namespace " +
                       "new or private protected public static switch throw trait try use var while xor " +
                       "die echo empty exit eval include include_once isset list require require_once return " +
                       "print unset __halt_compiler self static parent"),
    blockKeywords: keywords("catch do else elseif for foreach if switch try while"),
    multiLineStrings: true,
    hooks: {
      "$": function(stream) {
        stream.eatWhile(/[\w\$_]/);
        return "variable-2";
      },
      "<": function(stream, state) {
        if (stream.match(/<</)) {
          stream.eatWhile(/[\w\.]/);
          state.tokenize = heredoc(stream.current().slice(3));
          return state.tokenize(stream, state);
        }
        return false;
      },
      "#": function(stream) {
        while (!stream.eol() && !stream.match("?>", false)) stream.next();
        return "comment";
      },
      "@": function(stream) {
        stream.eatWhile(/[\w\@@_]/);
        return "variable-3";
      },
      "/": function(stream) {
        if (stream.eat("/")) {
          while (!stream.eol() && !stream.match("?>", false)) stream.next();
          return "comment";
        }
        return false;
      }
    }
  };

  CodeMirror.defineMode("php", function(config, parserConfig) {
    var htmlMode = CodeMirror.getMode(config, "text/html");
    var phpMode = CodeMirror.getMode(config, phpConfig);

    function dispatch(stream, state) {
      var isPHP = state.curMode == phpMode;
      if (stream.sol() && state.pending != '"') state.pending = null;
      
      if (!isPHP) {
        if (stream.match(/^<\?\w*/)) {
          state.curMode = phpMode;
          state.curState = state.php;
          return "meta";
        }
        if( config.PMEnabled == true) {
          state.curMode = phpMode;
          state.curState = state.php;
          return "meta";
        }
        if (state.pending == '"') {
          while (!stream.eol() && stream.next() != '"') {}
          var style = "string";
        } else if (state.pending && stream.pos < state.pending.end) {
          stream.pos = state.pending.end;
          var style = state.pending.style;
        } else {
          var style = htmlMode.token(stream, state.curState);
        }
        state.pending = null;
        var cur = stream.current(), openPHP = cur.search(/<\?/);
        if (openPHP != -1) {
          if (style == "string" && /\"$/.test(cur) && !/\?>/.test(cur)) state.pending = '"';
          else state.pending = {end: stream.pos, style: style};
          stream.backUp(cur.length - openPHP);
        }
        return style;
      } else if (isPHP && state.php.tokenize == null && stream.match("?>")) {
        state.curMode = htmlMode;
        state.curState = state.html;
        return "meta";
      } else {
          var token = phpMode.token(stream, state.curState);
          if(token == "keyword") {
            stream.eatWhile(/[\w\$_]/);
            var word = stream.current();
            known = PM_keywords[word];
            if (known) {
              return known.style;
            }else{
              known = php_keywords[word];
              if (known) {
                 return known.style;
              }else{
                 return "meta";
              }
            }
          }
        return token;
      }
    }

    return {
      startState: function() {
        var html = CodeMirror.startState(htmlMode), php = CodeMirror.startState(phpMode);
        return {html: html,
                php: php,
                curMode: parserConfig.startOpen ? phpMode : htmlMode,
                curState: parserConfig.startOpen ? php : html,
                pending: null};
      },

      copyState: function(state) {
        var html = state.html, htmlNew = CodeMirror.copyState(htmlMode, html),
            php = state.php, phpNew = CodeMirror.copyState(phpMode, php), cur;
        if (state.curMode == htmlMode) cur = htmlNew;
        else cur = phpNew;
        return {html: htmlNew, php: phpNew, curMode: state.curMode, curState: cur,
                pending: state.pending};
      },

      token: dispatch,

      indent: function(state, textAfter) {
        if ((state.curMode != phpMode && /^\s*<\//.test(textAfter)) ||
            (state.curMode == phpMode && /^\?>/.test(textAfter)))
          return htmlMode.indent(state.html, textAfter);
        return state.curMode.indent(state.curState, textAfter);
      },

      electricChars: "/{}:",

      innerMode: function(state) { return {state: state.curState, mode: state.curMode}; }
    };
  }, "htmlmixed", "clike");

  CodeMirror.defineMIME("application/x-httpd-php", "php");
  CodeMirror.defineMIME("application/x-httpd-php-open", {name: "php", startOpen: true});
  CodeMirror.defineMIME("text/x-php", phpConfig);
})();
