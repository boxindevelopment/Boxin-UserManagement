<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" 
 xmlns:v="urn:schemas-microsoft-com:vml"
 xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <!--[if gte mso 9]><xml>
   <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
   </o:OfficeDocumentSettings>
  </xml><![endif]-->
  <!-- fix outlook zooming on 120 DPI windows devices -->
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- So that mobile will display zoomed in -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- enable media queries for windows phone 8 -->
  <meta name="format-detection" content="date=no"> <!-- disable auto date linking in iOS 7-9 -->
  <meta name="format-detection" content="telephone=no"> <!-- disable auto telephone linking in iOS 7-9 -->
  <title>Boxin</title>
  <style type="text/css">
  /*start style*/
    .header,
    .title,
    .subtitle,
    .footer-text {
      font-family: Helvetica, Arial, sans-serif;
    }

    .header {
      font-size: 24px;
      font-weight: bold;
      padding: 12px 0px;
      color: #DF4726;
    }

    .footer-text {
      font-size: 12px;
      line-height: 16px;
      color: #aaaaaa;
    }
    .footer-text a {
      color: #aaaaaa;
    }

    .btn-password {  
      padding: 7px 0px;
      background-color: #bedefa;
      border-radius: 30px;
      margin: 0px 7%;
      font-weight: 800;
      /* box-shadow: -4px 0px 0px slategrey; */
      font-size: 15px;
      border-style: ridge;
    }

    .button {
      margin: 0px 30%;
      background-color: #313945;
      border-radius: 33px;
      padding: 5px 0px;
    }

    .container {
      width: 600px;
      max-width: 600px;
    }

    .container-padding {
      padding-left: 24px;
      padding-right: 24px;
    }

    .content {
      padding-top: 12px;
      padding-bottom: 12px;
      background-color: #ffffff;
    }

    code {
      background-color: #eee;
      padding: 0 4px;
      font-family: Menlo, Courier, monospace;
      font-size: 12px;
    }

    hr {
      border: 0;
      border-bottom: 1px solid #cccccc;
    }

    .hr {
      height: 1px;
      border-bottom: 1px solid #cccccc;
    }

    .title {
      font-size: 18px;
      font-weight: 600;
      color: #374550;
      text-align: center;
    }

    .subtitle {
      font-size: 16px;
      font-weight: 600;
      color: #2469A0;
    }
    .subtitle span {
      font-weight: 400;
      color: #999999;
    }

    .body-text {
      font-family: Helvetica, Arial, sans-serif;
      font-size: 14px;
      line-height: 20px;
      text-align: center;
      color: #333333;
    }

    a[href^="x-apple-data-detectors:"],
    a[x-apple-data-detectors] {
      color: inherit !important;
      text-decoration: none !important;
      font-size: inherit !important;
      font-family: inherit !important;
      font-weight: inherit !important;
      line-height: inherit !important;
    }
    /*end start style*/

    /*responsive*/
    body {
      margin: 0;
      padding: 0;
      -ms-text-size-adjust: 100%;
      -webkit-text-size-adjust: 100%;
    }

    table {
      border-spacing: 0;
    }

    table td {
      border-collapse: collapse;
    }

    .ExternalClass {
      width: 100%;
    }

    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {
      line-height: 100%;
    }

    .ReadMsgBody {
      width: 100%;
      background-color: #ebebeb;
    }

    table {
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }

    img {
      -ms-interpolation-mode: bicubic;
    }

    .yshortcuts a {
      border-bottom: none !important;
    }

    @media screen and (max-width: 599px) {
      .force-row,
      .container {
        width: 100% !important;
        max-width: 100% !important;
      }
    }
    @media screen and (max-width: 400px) {
      .container-padding {
        padding-left: 12px !important;
        padding-right: 12px !important;
      }
    }
    .ios-footer a {
      color: #aaaaaa !important;
      text-decoration: underline;
    }
    /*end responsive*/

  </style>
</head>
<body style="margin:0; padding:0;" bgcolor="#F0F0F0" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<!-- 100% background wrapper (grey background) -->
<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#F0F0F0">
  <tr>
    <td align="center" valign="top" bgcolor="#F0F0F0" style="background-color: #F0F0F0;">

      <br>

      <!-- 600px container (white background) -->
      <table border="0" width="600" cellpadding="0" cellspacing="0" class="container">
        <tr>
          <td class="container-padding header" align="left" style="background-color: #11A9EB; border-top-left-radius: 10px; border-top-right-radius: 10px;">
            <p style="margin: 0; color: white; text-align: center; text-shadow: 2px 2px black;">Boxin</p>
          </td>
        </tr>
        <tr>
          <td class="container-padding content" align="left">
            <br>
            <div class="title">Hello <br> <u>{{ $email }}</u></div>
            
            <br>

            <div class="body-text">
                Boxin received a request to reset your account password.
                <br>
                <b>New Password :</b>
                <br>
                <div class="button">
                  <div class="btn-password">
                      {{ $password }}
                  </div>
                </div>
                <br><br>

                Please login use this new password and then choose menu profile to change your password.
                <br><br>

                Please don't reply this email.
                <br><br>

                <hr>
                <strong>admin@boxin.com</strong>
            </div>

          </td>
        </tr>
        <tr>
          <td class="container-padding footer-text" align="left">
<!--             <br><br>
            Sample Footer text: &copy; 2015 Acme, Inc.
            <br><br>

            You are receiving this email because you opted in on our website. Update your <a href="#">email preferences</a> or <a href="#">unsubscribe</a>.
            <br><br>

            <strong>Acme, Inc.</strong><br>
            <span class="ios-footer">
              123 Main St.<br>
              Springfield, MA 12345<br>
            </span>
            <a href="http://www.acme-inc.com">www.acme-inc.com</a><br>
 -->
            <br><br>

          </td>
        </tr>
      </table><!--/600px container -->


    </td>
  </tr>
</table><!--/100% background wrapper-->

</body>
</html>