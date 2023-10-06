/*!
 * Internal Google Drive Picker Plugin.
 *
 * https://perfexcrm.com/
 *
 * Copyright (c) 2023 Marjan Stojanov
 *
 * Use ngrok for testing
 */

(function ($) {
    $.fn.googleDrivePicker = function (options) {
      var accessToken;
      var tokenClient;
  
      function initTokenClient() {
        tokenClient = google.accounts.oauth2.initTokenClient({
          client_id: settings.clientId,
          scope: settings.scope,
          callback: "",
        });
      }
  
      function initGooglePickerAPI(element) {
        if ($(element).attr("data-picker-inited")) {
          return;
        }
  
        gapi.load("picker", function () {
          $(element).attr("data-picker-inited", true);
  
          element.disabled = false;
  
          element.addEventListener("click", function () {
            if (!tokenClient) {
              initTokenClient();
            }
  
            createPicker();
          });
        });
      }
  
      function createPicker() {
        var showPicker = function () {
          var view = new google.picker.DocsView().setIncludeFolders(true);
          var uploadView = new google.picker.DocsUploadView().setIncludeFolders(
            true
          );
  
          if (settings.mimeTypes) {
            view.setMimeTypes(settings.mimeTypes);
            uploadView.setMimeTypes(settings.mimeTypes);
          }
  
          new google.picker.PickerBuilder()
            .addView(view)
            //.enableFeature(google.picker.Feature.MULTISELECT_ENABLED)
            .addView(uploadView)
            .setOAuthToken(accessToken)
            .setDeveloperKey(settings.developerKey)
            .setCallback(pickerCallback)
            .build()
            .setVisible(true);
  
          setTimeout(function () {
            $(".picker-dialog").css("z-index", 10002);
          }, 20);
        };
  
        // Request an access token
        tokenClient.callback = async (response) => {
          if (response.error !== undefined) {
            throw response;
          }
  
          accessToken = response.access_token;
  
          showPicker();
        };
  
        tokenClient.requestAccessToken({ prompt: "" });
      }
  
      function pickerCallback(data) {
        if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
          var retVal = [];
  
          data[google.picker.Response.DOCUMENTS].forEach(function (doc) {
            retVal.push({
              name: doc[google.picker.Document.NAME],
              link: doc[google.picker.Document.URL],
              mime: doc[google.picker.Document.MIME_TYPE],
            });
          });
  
          typeof settings.onPick == "function"
            ? settings.onPick(retVal)
            : window[settings.onPick](retVal);
        }
      }
  
      var settings = $.extend({}, $.fn.googleDrivePicker.defaults, options);
  
      return this.each(function () {
        if (settings.clientId) {
          if ($(this).data("on-pick")) {
            settings.onPick = $(this).data("on-pick");
          }
          initGooglePickerAPI($(this)[0]);
          $(this).css("opacity", 1);
        } else {
          // Not configured
          $(this).css("opacity", 0);
        }
      });
    };
  })(jQuery);
  
  $.fn.googleDrivePicker.defaults = {
    scope: "https://www.googleapis.com/auth/drive",
    mimeTypes: null,
    // The Browser API key obtained from the Google API Console.
    developerKey: "",
    // The Client ID obtained from the Google API Console. Replace with your own Client ID.
    clientId: "",
    onPick: function (data) {},
  };
  