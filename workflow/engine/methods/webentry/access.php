<?php
/**
 * This page is the WebEntry Access Point.
 */
if (empty($weUid)) {
    http_response_code(403);
    return;
}
$conf = new Configurations();
$configuration = $conf->getConfiguration(
    "ENVIRONMENT_SETTINGS",
    "",
    "",
    "",
    "",
    $outResult
);
$userInformationFormat = isset($outResult['format']) ? $outResult['format'] :
    '@lastName, @firstName (@userName)';
$webEntryModel = \WebEntryPeer::retrieveByPK($weUid);
?>
<html>
    <head>
        <link rel="stylesheet" href="/lib/pmdynaform/libs/bootstrap-3.1.1/css/bootstrap.min.css">
        <title><?php echo htmlentities($webEntryModel->getWeCustomTitle()); ?></title>
        <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
        <META HTTP-EQUIV="Expires" CONTENT="-1">
        <script src="/js/bluebird/bluebird.min.js"></script>
        <?php
            $oHeadPublisher = & headPublisher::getSingleton();
            echo $oHeadPublisher->getExtJsStylesheets(SYS_SKIN);
        ?>
        <style>
            html, body, iframe {
                border:none;
                width: 100%;
                top:0px;
                height:100%;
                margin: 0px;
                padding: 0px;
            }
            iframe {
                position: absolute;
                border:none;
                width: 100%;
                top:60px;
                bottom:0px;
                margin: 0px;
                padding: 0px;
            }
            .header {
                height: 60px;
            }
            .without-header .header {
                display:none;
            }
            .without-header #iframe {
                top:0px;
            }
            #avatar {
                background-color: buttonface;
                width: 48px;
                height: 48px;
                border-radius: 50%;
                border: 1px solid black;
                margin-left: 8px;
                margin-top: 4px;
                display: inline-block;
                position: absolute;
            }
            #userInformation {
                display: inline-block;
                margin-top: 20px;
                position: absolute;
                margin-left: 64px;
            }
            #logout {
                margin-top: 20px;
                position: absolute;
                margin-left: 64px;
                right: 8px;
            }
            #messageBox{
                position: absolute;
                left: 50%;
                margin-left: -260px;
                top: 96px;
            }
        </style>
    </head>
    <body class="without-header">
        <div class="header">
            <img id="avatar">
            <span class="logout"><a href="javascript:void(1)" id="userInformation"></a></span>
            <span class="logout"><a href="javascript:logout(1)" id="logout"><?php echo G::LoadTranslation('ID_LOGOUT'); ?></a></span>
        </div>
        <iframe id="iframe"></iframe>
        <form id="messageBox" class="formDefault formWE" method="post" style="display: none;">
            <div class="borderForm" style="width:520px; padding-left:0; padding-right:0; border-width:1px;">
                <div class="boxTop"><div class="a">&nbsp;</div><div class="b">&nbsp;</div><div class="c">&nbsp;</div></div>
                <div class="content" style="height:100%;">
                    <table width="99%">
                        <tbody><tr>
                                <td valign="top">
                                    <table cellspacing="0" cellpadding="0" border="0" width="100%">
                                        <tbody><tr>
                                                <td class="FormTitle" colspan="2" align=""><span id="form[TITLE]" name="form[TITLE]" pmfieldtype="title"><?php echo G::LoadTranslation('ID_ERROR'); ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="FormLabel" width="0"><label for="form[MESSAGE]"></label></td>
                                                <td class="FormFieldContent" width="520"><span id="errorMessage"></span></td>
                                            </tr>
                                            <tr id="messageBoxReset" style="display:none;">
                                                <td class="FormLabel" width="0"></td>
                                                <td class="FormFieldContent" width="520" style="text-align: right;"><button type="button" onclick="resetLocalData(true)"><?php echo G::LoadTranslation('ID_RESET'); ?></button></td>
                                            </tr>
                                        </tbody></table>
                                </td>
                            </tr>
                        </tbody></table>
                </div>
                <div class="boxBottom"><div class="a">&nbsp;</div><div class="b">&nbsp;</div><div class="c">&nbsp;</div></div>
            </div>
        </form>
        <script src="/lib/js/jquery-1.10.2.min.js"></script>
        <script>
            var weData = {};
            var resetLocalData = function (reload) {
                localStorage.removeItem('weData');
                weData={};
                if (reload) {
                    location.reload();
                }
            };
            var app = {
                $element:{
                    avatar: $("#avatar").get(0),
                    userInformation: $("#userInformation").get(0),
                    errorMessage: $("#errorMessage").get(0)
                },
                setAvatar:function(src){
                    this.$element.avatar.src=src;
                },
                getAvatar:function(){
                    return this.$avatar.src;
                },
                setUserInformation:function(textContent){
                    this.$element.userInformation.textContent=textContent;
                },
                getUserInformation:function(){
                    return this.$element.userInformation.textContent;
                },
                loadUserInformation:function(userInformation) {
                    var format = <?php echo G::json_encode($userInformationFormat); ?>;
                    this.setAvatar(userInformation.image);
                    for(var key in userInformation) {
                        format = format.replace("@"+key, userInformation[key]);
                    };
                    this.setUserInformation(format);
                },
                setErrorMessage:function(textContent){
                    this.$element.errorMessage.textContent=textContent;
                },
                getErrorMessage:function(){
                    return this.$element.errorMessage.textContent;
                }
            };
            function logout(reload, callback) {
                $.ajax({
                    url: '../login/login',
                    success: function () {
                        if (typeof callback==='function') {
                            callback();
                        }
                        if (reload) {
                            resetLocalData();
                            location.reload();
                        }
                    }
                });
            }
        </script>
        <script>
            !function () {
                var DEBUG_ENABLED = false;
                var processUid = <?php echo  G::json_encode($webEntryModel->getProUid()); ?>;
                var tasUid = <?php echo  G::json_encode($webEntryModel->getTasUid()); ?>;
                var weUid = <?php echo  G::json_encode($webEntryModel->getWeUid()); ?>;
                var forceLogin = <?php echo  G::json_encode($webEntryModel->getWeAuthentication()==='LOGIN_REQUIRED'); ?>;
                var isLogged = <?php echo  G::json_encode(!empty($_SESSION['USER_LOGGED'])); ?>;
                var currentLoggedIsGuest = <?php echo G::json_encode(!empty($_SESSION['USER_LOGGED']) && $_SESSION['USER_LOGGED'] === RBAC::GUEST_USER_UID); ?>;
                var closeSession = <?php echo  G::json_encode($webEntryModel->getWeCallback()==='CUSTOM_CLEAR'); ?>;
                var hideInformationBar = <?php echo  G::json_encode(!!$webEntryModel->getWeHideInformationBar()); ?>;
                if (!forceLogin) {
                    $("#logout").hide();
                }
                var onLoadIframe = function () {};
                var error = function(msg, showResetButton) {
                    app.setErrorMessage(msg);
                    if (showResetButton) {
                        $('#messageBoxReset').show();
                    } else {
                        $('#messageBoxReset').hide();
                    }
                    $('#messageBox').show();
                };
                var log = function() {
                    if (DEBUG_ENABLED) {
                        console.log.apply(console, arguments);
                    }
                };
                if (localStorage.weData) {
                    try {
                        weData = JSON.parse(localStorage.weData);
                        if (weData.TAS_UID!==tasUid || !weData.APPLICATION || !weData.INDEX) {
                            //TAS_UID is different, reset.
                            resetLocalData();
                        }
                    } catch (e) {
                        //corrupt weData, reset.
                        resetLocalData();
                    }
                }
                $("#iframe").load(function (event) {
                    onLoadIframe(event);
                });
                var getContentDocument = function (iframe) {
                    return (iframe.contentDocument) ?
                      iframe.contentDocument :
                      iframe.contentWindow.document;
                };
                var open = function (url, callback) {
                    return new Promise(function (resolve, reject) {
                        var iframe = document.getElementById("iframe");
                        if (typeof callback === 'function') {
                            iframe.style.opacity = 0;
                            onLoadIframe = (function () {
                                return function (event) {
                                    if (callback(event, resolve, reject)) {
                                        iframe.style.opacity = 1;
                                    }
                                };
                            })();
                        } else {
                            iframe.style.opacity = 1;
                            onLoadIframe = function () {};
                        }
                        //This code is to prevent error at back history
                        //in Firefox
                        setTimeout(function(){iframe.src = url;}, 0);
                        window.fullfill = function () {
                            resolve.apply(this, arguments);
                        };
                        window.reject = function () {
                            reject(this, arguments);
                        };
                    });
                };
                var verifyLogin = function () {
                    if (forceLogin) {
                        return login();
                    } else {
                        return anonymousLogin();
                    }
                };
                var login = function () {
                    return new Promise(function (logged, failure) {
                        if (!isLogged || currentLoggedIsGuest) {
                            log("login");
                            open('../login/login?inIFrame=1&u=' + encodeURIComponent(location.pathname + '/../../webentry/logged'))
                              .then(function (userInformation) {
                                  logged(userInformation);
                              })
                              .catch(function () {
                                  failure();
                              });
                        } else {
                            log("logged");
                            open('../webentry/logged')
                              .then(function (userInformation) {
                                  logged(userInformation);
                              })
                              .catch(function () {
                                  failure();
                              });
                        }
                    });
                };
                var anonymousLogin = function () {
                    return new Promise(function (resolve, failure) {
                        log("anonymousLogin");
                        $.ajax({
                            url: '../services/webentry/anonymousLogin',
                            method: 'get',
                            dataType: 'json',
                            data: {
                                we_uid: weUid
                            },
                            success: function (userInformation) {
                                resolve(userInformation);
                            },
                            error: function (data) {
                                failure(data);
                            }
                        });
                    });
                };
                var loadUserInformation = function (userInformation) {
                    return new Promise(function (resolve, reject) {
                        log("userInformation:", userInformation);
                        app.loadUserInformation(userInformation);
                        resolve();
                    });
                };
                var checkWebEntryCase = function (userInformation) {
                    return new Promise(function (resolve, reject) {
                        if (localStorage.weData) {
                            log("checkWebEntryCase");
                            $.ajax({
                                url: '../services/webentry/checkCase',
                                method: 'post',
                                dataType: 'json',
                                data: {
                                    app_uid: weData.APPLICATION,
                                    del_index: weData.INDEX
                                },
                                success: function (data) {
                                    log("check:", data);
                                    if (!data.check) {
                                        resetLocalData();
                                    }
                                    resolve();
                                },
                                error: function () {
                                    resetLocalData();
                                    resolve();
                                }
                            });
                        } else {
                            resolve();
                        }
                    });
                };
                var initCase = function () {
                    return new Promise(function (resolve, reject) {
                        if (!hideInformationBar) {
                            $("body").removeClass("without-header");
                        }
                        if (!localStorage.weData) {
                            log("initCase");
                            $.ajax({
                                url: '../cases/casesStartPage_Ajax',
                                method: 'post',
                                dataType: 'json',
                                data: {
                                    action: 'startCase',
                                    processId: processUid,
                                    taskId: tasUid
                                },
                                success: function (data) {
                                    data.TAS_UID = tasUid;
                                    localStorage.weData = JSON.stringify(data);
                                    resolve(data);
                                },
                                error: function () {
                                    reject();
                                }
                            });
                        } else {
                            log("openCase");
                            resolve(weData);
                        }
                    });
                };
                var casesStep = function (data) {
                    return new Promise(function (resolve, reject) {
                        log("casesStep");
                        open(
                            '../cases/cases_Open?APP_UID=' + encodeURIComponent(data.APPLICATION) +
                            '&DEL_INDEX=' + encodeURIComponent(data.INDEX) +
                            '&action=draft',
                            function (event, resolve, reject) {
                                var contentDocument = getContentDocument(event.target);
                                var stepTitle = contentDocument.getElementsByTagName("title");
                                if (!stepTitle || !stepTitle.length || stepTitle[0].textContent === 'Runtime Exception.') {
                                    if (contentDocument.location.search.match(/&POSITION=10000&/)) {
                                        //Catch error if webentry was deleted.
                                        reject();
                                        return false;
                                    }
                                }
                                return true;
                            }
                        ).then(function (callbackUrl) {
                              resolve(callbackUrl);
                        })
                        .catch(function () {
                            reject();
                        });
                    });
                };
                var routeWebEntry = function (callbackUrl) {
                    return new Promise(function (resolve, reject) {
                        log("routeWebEntry", callbackUrl);
                        resolve(callbackUrl);
                    });
                };
                var closeWebEntry = function (callbackUrl) {
                    return new Promise(function (resolve, reject) {
                        log("closeWebEntry");
                        resetLocalData();
                        if (closeSession) {
                            //This code is to prevent error at back history
                            //in Firefox
                            $("#iframe").hide();
                            $("#iframe").attr("src", "../login/login?inIFrame=1");
                            logout(false, function() {
                                resolve(callbackUrl);
                            });
                        } else {
                            //This code is to prevent error at back history
                            //in Firefox
                            open("../webentry/logged", function() {
                                resolve(callbackUrl);
                            });
                        }
                    });
                };
                var redirectCallback = function (callbackUrl) {
                    return new Promise(function (resolve, reject) {
                        log("redirect: "+callbackUrl);
                        location.href = callbackUrl;
                        resolve();
                    });
                };
                //Errors
                var errorLogin = function () {
                    return new Promise(function (resolve, reject) {
                        log("errorLogin");
                        var msg = <?php echo G::json_encode(G::LoadTranslation('ID_EXCEPTION_LOG_INTERFAZ')); ?>;
                        msg = msg.replace("{0}", "LOGIN");
                        error(msg);
                        resetLocalData();
                    });
                };
                var errorLoadUserInfo = function () {
                    return new Promise(function (resolve, reject) {
                        log("errorLoadUserInfo");
                        var msg = <?php echo G::json_encode(G::LoadTranslation('ID_EXCEPTION_LOG_INTERFAZ')); ?>;
                        msg = msg.replace("{0}", "USR001");
                        error(msg);
                        resetLocalData();
                    });
                };
                var errorCheckWebEntry = function () {
                    return new Promise(function (resolve, reject) {
                        log("errorCheckWebEntry");
                        var msg = <?php echo G::json_encode(G::LoadTranslation('ID_EXCEPTION_LOG_INTERFAZ')); ?>;
                        msg = msg.replace("{0}", "WEE001");
                        error(msg);
                        resetLocalData();
                    });
                };
                var errorInitCase = function () {
                    return new Promise(function (resolve, reject) {
                        log("error Init case");
                        var msg = <?php echo G::json_encode(G::LoadTranslation('ID_EXCEPTION_LOG_INTERFAZ')); ?>;
                        msg = msg.replace("{0}", "INIT001");
                        error(msg);
                        resetLocalData();
                    });
                };
                var errorStep = function () {
                    return new Promise(function (resolve, reject) {
                        log("Step Error");
                        var msg = <?php echo G::json_encode(G::LoadTranslation('ID_EXCEPTION_LOG_INTERFAZ')); ?>;
                        msg = msg.replace("{0}", "STEP001");
                        error(msg);
                        resetLocalData();
                    });
                };
                var errorRouting = function () {
                    return new Promise(function (resolve, reject) {
                        log("errorRouting");
                        var msg = <?php echo G::json_encode(G::LoadTranslation('ID_EXCEPTION_LOG_INTERFAZ')); ?>;
                        msg = msg.replace("{0}", "ROU001");
                        error(msg);
                        resetLocalData();
                    });
                };
                //Execute WebEntry Flow
                verifyLogin().catch(errorLogin)
                  .then(loadUserInformation).catch(errorLoadUserInfo)
                  .then(checkWebEntryCase).catch(errorCheckWebEntry)
                  .then(initCase).catch(errorInitCase)
                  .then(casesStep).catch(errorStep)
                  .then(routeWebEntry).catch(errorRouting)
                  .then(closeWebEntry)
                  .then(redirectCallback);
            }();
        </script>
    </body>
</html>