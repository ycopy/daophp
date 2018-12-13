<?php
    error_reporting(0);
    ini_set('display_errors', 0);
    header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
    header('Status: 403 Forbidden');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>403 Forbidden</title>
        <style type="text/css">
            <!--
            .t{
                    font-family: Verdana, Arial, Helvetica, sans-serif;
                    color: #CC0000;
            }
            .c{
                    font-family: Verdana, Arial, Helvetica, sans-serif;
                    font-size: 11px;
                    font-weight: normal;
                    color: #000000;
                    line-height: 18px;
                    text-align: center;
                    border: 1px solid #CCCCCC;
                    background-color: #FFFFEC;
            }
            body{
                    background-color: #FFFFFF;
                    margin-top: 100px;
            }
            -->
        </style>
    </head>
    <body>
        <div align="center">
          <h2><span class="t">403 Forbidden</span></h2>
          <table border="0" cellpadding="8" cellspacing="0" width="460">
            <tbody>
              <tr>
                <td class="c">The requested URL was forbidden on this server.<br /><?php echo $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];?></td>
              </tr>
            </tbody>
          </table>
        </div>
    </body>
</html>