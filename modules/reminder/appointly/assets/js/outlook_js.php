<script type="text/javascript" src="https://alcdn.msauth.net/lib/1.3.4/js/msal.js"></script>
<script>
     var outlookConf = {
          msalConfig: {
               auth: {
                    clientId: "<?= $appointly_outlook_client_id; ?>",
                    authority: "https://login.microsoftonline.com/common",
                    redirectUri: admin_url + 'appointly/appointments',
               },
               cache: {
                    cacheLocation: "localStorage",
                    storeAuthStateInCookie: true
               },
               system: {
                    loadFrameTimeout: 50000
               }
          },
          graphConfig: {
               graphMeEndpoint: "https://graph.microsoft.com/v1.0/me"
          },
          requestObj: {
               scopes: ["openid", "User.ReadWrite", "Calendars.ReadWrite"]
          }
     }
     var myMSALObj = new Msal.UserAgentApplication(outlookConf.msalConfig);
     // Register Callbacks for redirect flow
     myMSALObj.handleRedirectCallback(authRedirectCallBack);

     function signInToOutlook() {
          myMSALObj.loginPopup(outlookConf.requestObj).then(function(loginResponse) {
               if (loginResponse.fromCache === false) {
                    window.location.reload();
               }
               //Successful login
               checkOutlookAuthentication();
               //Call MS Graph using the token in the response
               // acquireTokenPopupAndCallMSGraph();
          }).catch(function(error) {
               renderErrorToConsole(error);
          });
     }

     function outlookSignOut() {
          myMSALObj.logout();
          var msalCookies = document.cookie.split(/=[^;]*(?:;\s*|$)/);

          // Remove all msal cookies
          for (var i = 0; i < msalCookies.length; i++) {
               if (/^msal/.test(msalCookies[i])) {
                    document.cookie = msalCookies[i] + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/';
               }
          }
     }

     function acquireTokenPopupAndCallMSGraph() {
          //Always start with acquireTokenSilent to obtain a token in the signed in user from cache
          myMSALObj.acquireTokenSilent(outlookConf.requestObj).then(function(tokenResponse) {
               callMSGraph(outlookConf.graphConfig.graphMeEndpoint, tokenResponse.accessToken);
               // getOutlookEvents("https://graph.microsoft.com/v1.0/me/calendar/events", tokenResponse.accessToken);
          }).catch(function(error) {
               renderErrorToConsole(error);
               // Upon acquireTokenSilent failure (due to consent or interaction or login required ONLY)
               // Call acquireTokenPopup(popup window)
               if (requiresInteraction(error.errorCode)) {
                    document.getElementById('sign_in_outlook').innerHTML = "<?= _l('appointment_login_to_outlook'); ?>";
                    myMSALObj.acquireTokenPopup(outlookConf.requestObj).then(function(tokenResponse) {
                         callMSGraph(outlookConf.graphConfig.graphMeEndpoint, tokenResponse.accessToken);
                         // getOutlookEvents("https://graph.microsoft.com/v1.0/me/calendar/events", tokenResponse.accessToken);
                    }).catch(function(error) {
                         renderErrorToConsole(error);
                    });
               }
          });
     }

     function callMSGraph(url, accessToken) {
          var msAccessToken = document.getElementById('ms-access-token');
          if (msAccessToken !== null) {
               msAccessToken.value = accessToken;
          }
          var xmlHttp = new XMLHttpRequest();
          /**
           * True for asynchronous
           */
          xmlHttp.open("GET", url, true);
          xmlHttp.setRequestHeader('Authorization', 'bearer ' + accessToken);
          xmlHttp.send();
     }

     function checkOutlookAuthentication() {
          var loginButton = document.getElementById('sign_in_outlook');

          if (myMSALObj.getAccount()) {
               loginButton.innerHTML = "<?= _l('appointment_logout_from_outlook'); ?>";
               loginButton.setAttribute('onclick', 'outlookSignOut();');
               loginButton.setAttribute('data-toggle', 'tooltip');
               loginButton.setAttribute('title', "<?= _l('appointments_outlook_revoke'); ?>");
               loginButton.insertAdjacentHTML('afterBegin', '<i class="fa fa-envelope fixLine" aria-hidden="true"></i>');
          } else {
               loginButton.innerHTML = "<?= _l('appointment_logout_from_outlook'); ?>";
          }

     }

     function authRedirectCallBack(error, response) {
          if (error) {
               renderErrorToConsole(error);
          } else {
               if (response.tokenType === "access_token") {
                    callMSGraph(outlookConf.graphConfig.graphMeEndpoint, response.accessToken);
               }
          }
     }

     function requiresInteraction(errorCode) {
          if (!errorCode || !errorCode.length) {
               return false;
          }
          return errorCode === "consent_required" ||
               errorCode === "interaction_required" ||
               errorCode === "login_required";
     }

     async function outlookAddOrUpdateEvent(data) {

          if (!isOutlookLoggedIn()) {
               return false;
          }

          var accessToken = document.getElementById('ms-access-token').value;
          var eventId = document.getElementById('ms-outlook-event-id').value;

          var paramData = [];
          var outlook_attendees = [];
          var normal_date = '';
          var event = '';
          var attendees_data = '';

          data.map(function(form) {
               if (form['name'] == 'attendees[]') {
                    outlook_attendees.push(form['value'])
               }
               paramData[form['name']] = form['value'];
          });

          paramData.attendees = [outlook_attendees];

          delete paramData['attendees[]'];


          var attendeeDataPromisse = new Promise(function(resolve, reject) {
               return resolve($.post(site_url + 'appointly/appointments/getAttendeeData', {
                    ids: Object.values(outlook_attendees)
               }));
          })

          attendeeDataPromisse.then(function(attendees) {
               // Add selected contact to attendee list
               if ($('body').find('#div_email input').val().length > 0) {
                    var clientContactLead = {
                         emailAddress: {
                              address: $('body').find('#div_email input').val(),
                              name: $('body').find('#div_name input').val(),
                         }
                    }
                    attendees.push(clientContactLead);
               }
               return attendees;
          });

          var attendees_data = await attendeeDataPromisse;

          paramData.date = moment(paramData.date, ['DD/MM/YYYY HH:mm:ss', 'DD.MM.YYYY HH:mm:ss', 'MM-DD-YYYY HH:mm:ss', 'MM.DD.YYYY HH:mm:ss', 'MM/DD/YYYY HH:mm:ss', 'YYYY-MM-DD HH:mm:ss']).format();

          event = {
               subject: paramData.subject,
               body: {
                    contentType: "HTML",
                    content: paramData.description
               },
               start: {
                    dateTime: paramData.date,
                    timeZone: app.options.timezone
               },
               end: {
                    dateTime: paramData.date,
                    timeZone: app.options.timezone
               },
               location: {
                    displayName: (paramData.address) ? paramData.address : ''
               },
               attendees: attendees_data,
               Importance: "Normal",
               HasAttachments: false,
               isReminderOn: true,
               reminderMinutesBeforeStart: 60
          };

          var url = 'https://graph.microsoft.com/v1.0/me/events/';
          var requestType = 'POST';

          // used for updating event
          if (typeof eventId != 'undefined' && eventId != '') {
               requestType = 'PATCH'
               url += eventId;
          }

          var dfd = jQuery.Deferred();
          var promise = dfd.promise();

          $.ajax({
               url,
               type: requestType,
               headers: {
                    'Content-Type': 'application/json;charset=UTF-8',
                    'Authorization': 'Bearer ' + accessToken,
               },
               data: JSON.stringify(event)
          }).done(function(appointment) {
               if (appointment.id) {
                    $.post(site_url + 'appointly/appointments/newOutlookEvent', {
                         outlook_event_id: appointment.id,
                         outlook_calendar_link: appointment.webLink,
                    }).done(function(r) {
                         dfd.resolve();
                         if (r.result) {
                              if (requestType == 'POST') {
                                   alert_float('success', '<?= _l("appointment_created"); ?>');
                              } else {
                                   alert_float('success', '<?= _l("appointment_updated"); ?>');
                              }
                              setTimeout(() => {
                                   window.location.reload();
                              }, 500);
                         }
                    });
               }
          }).fail(function(error) {
               renderErrorToConsole(error);
          });
          promise.then(function() {
               return dfd.promise();
          })
     }

     function addToOutlookNewEventFromUpdate(data, appointment_id) {

          if (!isOutlookLoggedIn()) {
               return false;
          }

          var accessToken = document.getElementById('ms-access-token').value;

          var paramData = [];
          var outlook_attendees = [];

          data.map(function(form) {
               if (form['name'] == 'attendees[]') {
                    outlook_attendees.push(form['value'])
               }
               paramData[form['name']] = form['value'];
          });

          paramData.attendees = [outlook_attendees];

          delete paramData['attendees[]'];

          var attendeeDataPromisse = new Promise(function(resolve, reject) {
               return resolve($.post(site_url + 'appointly/appointments/getAttendeeData', {
                    ids: Object.values(outlook_attendees)
               }));
          })

          attendeeDataPromisse.then(function(attendees) {
               // Add selected contact to attendee list
               if ($('body').find('#div_email input').val().length > 0) {
                    var clientContactLead = {
                         emailAddress: {
                              address: $('body').find('#div_email input').val(),
                              name: $('body').find('#div_name input').val(),
                         }
                    }
                    attendees.push(clientContactLead);
               }

               paramData.date = moment(paramData.date, ['DD/MM/YYYY HH:mm:ss', 'DD.MM.YYYY HH:mm:ss', 'MM-DD-YYYY HH:mm:ss', 'MM.DD.YYYY HH:mm:ss', 'MM/DD/YYYY HH:mm:ss', 'YYYY-MM-DD HH:mm:ss']).format();

               var event = {
                    subject: paramData.subject,
                    body: {
                         contentType: "HTML",
                         content: paramData.description
                    },
                    start: {
                         dateTime: paramData.date,
                         timeZone: app.options.timezone
                    },
                    end: {
                         dateTime: paramData.date,
                         timeZone: app.options.timezone
                    },
                    location: {
                         displayName: (paramData.address) ? paramData.address : ''
                    },
                    attendees: attendees,
                    Importance: "Normal",
                    HasAttachments: false,
                    isReminderOn: true,
                    reminderMinutesBeforeStart: 60
               };


               var url = 'https://graph.microsoft.com/v1.0/me/events/';
               var requestType = 'POST';

               $.ajax({
                    url,
                    type: requestType,
                    headers: {
                         'Content-Type': 'application/json;charset=UTF-8',
                         'Authorization': 'Bearer ' + accessToken,
                    },
                    data: JSON.stringify(event),
                    beforeSend: function() {
                         $('button[type="submit"], button.close_btn').prop('disabled', true);
                         $('#appointment-form .modal-body').addClass('filterBlur');
                         $('.modal-title').html("<?= _l('appointment_please_wait'); ?>");
                         $('#addToOutlookBtn').html('<i class="fa fa-refresh fa-spin fa-fw"></i>');
                    }
               }).done(function(appointment) {
                    if (appointment.id) {
                         $.post(site_url + 'appointly/appointments/updateAndAddExistingOutlookEvent', {
                              outlook_event_id: appointment.id,
                              outlook_calendar_link: appointment.webLink,
                              appointment_id: appointment_id,
                         }).done(function(r) {
                              if (r.result == true) {
                                   alert_float('success', '<?= _l("appointment_added_to_outlook"); ?>');
                                   window.location.reload();
                              }
                         });
                    }
               }).fail(function(error) {
                    renderErrorToConsole(error);
               });
          });
     }

     function deleteOutlookEvent(id) {
          if (isOutlookLoggedIn()) {
               myMSALObj.acquireTokenSilent(outlookConf.requestObj).then(function(Response) {
                    if (Response.accessToken) {
                         $.ajax({
                                   url: 'https://graph.microsoft.com/v1.0/me/events/' + id,
                                   type: 'DELETE',
                                   beforeSend: function(xhr) {
                                        xhr.setRequestHeader('Authorization', 'Bearer ' + Response.accessToken);
                                   }
                              }).done(function() {
                                   // 
                              })
                              .fail(function(error) {
                                   renderErrorToConsole(error);
                              });
                    } else {
                         return false;
                    }
               });
          }
     }

     /**
      * Fetch outlook events
      */
     function getOutlookEvents(url, token) {
          $.ajax({
                    url,
                    type: 'GET',
                    beforeSend: function(xhr) {
                         xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                    },
                    data: {}
               }).done(function() {
                    // done
               })
               .fail(function(error) {
                    renderErrorToConsole(error);
               });
     }

     /**
      * Error Log renderer
      */
     function renderErrorToConsole(error) {
          console.log(error)
     }

     // Browser check variables
     var ua = window.navigator.userAgent;
     var msie = ua.indexOf('MSIE ');
     var msie11 = ua.indexOf('Trident/');
     var msedge = ua.indexOf('Edge/');
     var isIE = msie > 0 || msie11 > 0;
     var isEdge = msedge > 0;

     //If you support IE, our recommendation is that you sign-in using Redirect APIs
     //If you as a developer are testing using Edge InPrivate mode, please add "isEdge" to the if check

     // can change this to default an experience outside browser use
     var loginType = isIE ? "REDIRECT" : "POPUP";

     // runs on page load, change config to try different login types to see what is best for your application
     if (loginType === 'POPUP') {
          if (myMSALObj.getAccount()) { // avoid duplicate code execution on page load in case of iframe and popup window.
               checkOutlookAuthentication();
               // acquireTokenPopupAndCallMSGraph();
          }
     } else if (loginType === 'REDIRECT') {
          document.getElementById("sign_in_outlook").onclick = function() {
               myMSALObj.loginRedirect(outlookConf.requestObj);
          };

          // avoid duplicate code execution on page load in case of iframe and popup window.
          if (myMSALObj.getAccount() && !myMSALObj.isCallback(window.location.hash)) {
               console.info('AVOIDING DUPLICATE CODE EXECUTION.....');
               checkOutlookAuthentication();
          }
     } else {
          console.error('Please set a valid login type');
     }
</script>