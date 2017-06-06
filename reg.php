<?php
session_start();
if ($_SESSION['authorized']<>1) //если не авторизован
{
  echo "
<h2>Регистрация!</h2>
  <form action=\"index.php\" method=\"post\">
    <table border=0 align=\"center\">
      <tbody>
        <tr height=50 valign=\"bottom\">
          <td align=\"center\" colspan=\"2\">
            Добро пожаловать!
          </td>
        </tr>
        <tr>
          <td align=\"center\" colspan=\"2\">
            Для регистрации на сайте, заполните, пожалуйста, все поля
          </td>
        </tr>
        <tr>
          <td align=\"right\">
            Логин:
          </td>
          <td align=\"left\">
            <input type=\"text\" id=\"logR\" name=\"logR\">
          </td>
        </tr>
        <tr>
          <td align=\"right\">
            Имя:
          </td>
          <td align=\"left\">
            <input type=\"text\" id=\"namR\" name=\"namR\">
          </td>
        </tr>
        <tr>
          <td align=\"right\">
            Пароль:
          </td>
          <td align=\"left\">
            <input type=\"password\" id=\"pasR\" name=\"pasR\">
          </td>
        </tr>
        <tr>
          <td align=\"center\" colspan=\"2\">
            <input type=\"submit\" id=\"sub1\" value=\"Регистрация\" onclick=\"alert(submitClick())\">
          </td>
        </tr>
      </tbody>
    </table>
  </form>";
}
else { //если авторизован
  echo "<h2>Регистрация!</h2><center><h3>Привет, ".$_SESSION['username']."!</h3></center><p>Ты уже авторизован.</p>";
}
?>
<script type="text/javascript">
    $(function (){
      $('form').submit(function (){
        var v_log = document.getElementById('logR').value;
        var v_nam = document.getElementById('namR').value;
        var v_pas = document.getElementById('pasR').value;
        var message = 'ВНИМАНИЕ! \n\r \n\r';
        if (v_log === '' || v_nam === '' || v_pas === '' || v_pas_r ==='')
          {message += 'Заполните все поля! \n\r';}
        if(/^[a-zA-Z1-9_]+$/.test(v_log) === false)
          {message += 'В логине должны быть только латинские буквы и символы нижнего подчеркивания \n\r';}
        if (parseInt(v_log.substr(0, 1)))
          {message += 'Логин должен начинаться с буквы \n\r';}
        if (/^[a-zA-Zа-яА-Я_-\s]+$/.test(v_nam) === false)
          {message += 'В имени нельзя испольовать цифры и спецсимволы \n\r';}
        if (/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/.test(v_pas) === false)
          {message += 'Пароль должен содержать минимум 8 символов с использованием строчных и прописных латинских букв, цифр и спецсимволов \n\r';}
        if (message !== 'ВНИМАНИЕ! \n\r \n\r')
          {alert(message);
          return false;}
        else
          {return true;}
      });
    });
</script>
