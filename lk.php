<?php
session_start();
if ($_SESSION['authorized']<>1) //если не авторизован
{
  echo "<form action=\"index.php\" method=\"post\">
      <table border=0 align=\"center\">
        <tbody>
          <tr height=50 valign=\"bottom\">
            <td align=\"center\">
              Добро пожаловать!
            </td>
          </tr>
          <tr>
            <td>
              &nbsp Логин: <input type=\"text\" id=\"log\" name=\"log\">
            </td>
          </tr>
          <tr>
            <td>
              Пароль: <input type=\"password\" id=\"pas\" name=\"pas\">
            </td>
          </tr>
          <tr>
            <td align=\"center\">
              <input type=\"hidden\" name=\"autorizform\" value=\"OK\">
              <input type=\"submit\" id=\"sub2\" value=\"Войти\">
            </td>
          </tr>
        </tbody>
      </table>
    </form>";
}
else { //если авторизован уже
  //$_SESSION['lk'] = 1;
  //header ('Location: index.php');
  echo "<h2>Привет, ".$_SESSION['username']."!</h2><p>Здесь ты можешь изменить свои данные или добавить в базу новый рецепт.</p>
  <center><h3>Изменить данные</h3></center>
  <form action=\"index.php\" method=\"post\" id=\"fchlk\">
    <table border=0 align=\"center\">
      <tbody>
        <tr height=25 valign=\"bottom\">
        <td align=\"right\">
          Новое имя:
        </td>
        <td align=\"left\">
          <input type=\"text\" id=\"namNEW\" name=\"namNEW\">
        </td>
      </tr>
      <tr>
        <td align=\"right\">
          Новый пароль:
        </td>
        <td align=\"left\">
          <input type=\"password\" id=\"pasNEW\" name=\"pasNEW\">
        </td>
      </tr>
      <td align=\"right\">
        Повторите пароль:
      </td>
      <td align=\"left\">
          <input type=\"password\" id=\"pas_rNEW\" name=\"pas_rNEW\">
      </td>
      <tr>
        <td align=\"center\" colspan=\"2\">
          <input type=\"hidden\" name=\"changelkform\" value=\"OK\">
          <input type=\"submit\" id=\"sub1\" value=\"Изменить\">
        </td>
      </tr>
    </tbody>
  </table>
</form>
<center><h3>Добавить рецепт</h3></center>
<form action=\"index.php\" method=\"post\" id=\"fadrec\">
  <table border=0 align=\"center\">
    <tbody>
      <tr height=25 valign=\"bottom\">
      <td align=\"center\">
        Название рецепта:
      </td>
    </tr>
    <tr>
      <td align=\"center\">
        <input type=\"text\" id=\"namRec\" name=\"namRec\" size=50>
      </td>
    </tr>
    <tr>
      <td align=\"center\">
        Ингридиенты (пример оформления: \"Соль: 250 гр., Сахар: по вкусу\"):
      </td>
    </tr>
    <tr>
      <td align=\"center\">
        <input type=\"text\" id=\"ingrRec\" name=\"ingrRec\" size=150>
      </td>
    </tr>
    <tr>
    <td align=\"center\">
      Рецепт:
    </td>
    </tr>
    <tr>
    <td align=\"center\">
        <textarea name=\"recRec\" cols=150 rows=15></textarea>
    </td>
    </tr>
    <tr>
      <td align=\"center\" colspan=\"2\">
        <input type=\"hidden\" name=\"addreceptform\" value=\"OK\">
        <input type=\"submit\" id=\"sub1\" value=\"Добавить рецепт\">
      </td>
    </tr>
  </tbody>
</table>
</form>
<center><form action=\"index.php\" method=\"post\">
  <input type=\"hidden\" name=\"exitform\" value=\"OK\">
  <input type=\"submit\" id=\"sub1\" value=\"Выход из профиля\">
</form></center>
";
}
?>
