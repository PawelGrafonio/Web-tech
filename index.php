<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css" type="text/css">
    <link rel="stylesheet" href="style_menu.css" type="text/css">
    <title>Рецепты для вас и вашей семьи!</title>
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="js.js"></script>

</head>

<div id = "wrapper">
  <img src="fff.png" height="180" width="180">
   <h2>Рецепты для вас и вашей семьи</h2>
  <ul id="nav">
    <li><a href="recepts.php">Рецепты</a></li>
    <li><a href="search_f.php">По блюдам</a></li>
    <li><a href="search_i.php">По ингридиентам</a></li>
    <li><a href="lk.php">Профиль</a></li>
    <li><a href="reg.php">Регистрация</a></li>

  </ul>
</div>

<div id="content">
<?php
    //линк для использования mysqli
    $link = mysqli_connect("localhost", "root", "", "site");
    if (count($_POST) > 0) //если пришли после какой-то формы
    {
      if(strlen($_POST['logR'])!=0 && strlen($_POST['namR'])!=0 && strlen($_POST['pasR'])!=0) //если с формы регистрации
      {
    if(isset($_POST['submit']))
{
        $login = htmlspecialchars($_POST['logR']);
        $name = htmlspecialchars($_POST['namR']);
        $pas = htmlspecialchars($_POST['pasR']);
    $err = array();
    # проверям логин
    if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['logR']))
    {
        $err[] = "Логин может состоять только из букв английского алфавита и цифр";
    }
    if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['NameR']))
    {
        $err[] = "имя может состоять только из букв английского алфавита и цифр";
    }
    if(strlen($_POST['login']) < 3 or strlen($_POST['logR']) > 30)
    {
        $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
    }
    # проверяем, не сущестует ли пользователя с таким именем
    $query = mysqli_query("SELECT COUNT(user_id) FROM users WHERE login='".mysqli_real_escape_string($_POST['logR'])."'");
    if(mysql_result($query, 0) > 0)
    {
        $err[] = "Пользователь с таким логином уже существует в базе данных";
    }
    # Если нет ошибок, то добавляем в БД нового пользователя
    if(count($err) == 0)
    {
        $login = $_POST['logR'];
        # Убераем лишние пробелы и делаем двойное шифрование
        $password = md5(md5(trim($_POST['password'])));
        mysqli_query("INSERT INTO users SET login='".$login."', password='".$password."'");
        header("Location: login.php"); exit();
    }
    else
    {
        print "<b>При регистрации произошли следующие ошибки:</b><br>";
        foreach($err AS $error)
        {
            print $error."<br>";
        }
    }
}

      }
      elseif ($_POST['search_fform']=="OK") { //если с формы поиска по блюду
        if (strlen($_POST['s2'])==0)
        {unset($_POST); DIE("<p>Не введены данные для поиска!</p>");}
        else
        {
          $name = htmlspecialchars($_POST['s2']);
          $recepts = mysqli_query ($link, "SELECT name, ingr, rec_text FROM recepts WHERE name like '%$name%'");
          if (mysqli_num_rows($recepts) != 0)
          {
            echo "<center><h3>Рецепты по запросу ".$name."</h3></center><br>";
            for ($i=0; $i < mysqli_num_rows($recepts) ; $i++)
            {
              mysqli_data_seek($recepts, $i);
              $row = mysqli_fetch_row($recepts);
              echo "<h3>".$row[0]."</h3><br><b>Ингридиенты:</b> ".$row[1]."<br><b>Рецепт:</b><br>".$row[2]."<br>&nbsp<br>";
            }
          }
          else
          {echo "<p>Не найден ни один рецепт по запросу ".$name."</p>";}
          unset($_POST);
        }
      }
      elseif ($_POST['search_iform']=="OK") { //если с формы поиска по ингридиентам
        if (strlen($_POST['s1'])==0)
        {unset($_POST); DIE("<p>Не введены данные для поиска!</p>");}
        else
        {
          $ingres = explode(", ", htmlspecialchars($_POST['s1']));
          $query = "SELECT name, ingr, rec_text FROM recepts WHERE 1";
          for ($i=0; $i < count($ingres) ; $i++)
          {
            $query = $query . " AND ingr like '%$ingres[$i]%'";
          }
          $recepts = mysqli_query ($link, $query);
          if (mysqli_num_rows($recepts) != 0)
          {
            echo "<center><h3>Рецепты по запросу ".$_POST['s1']."</h3></center><br>";
            for ($i=0; $i < mysqli_num_rows($recepts) ; $i++)
            {
              mysqli_data_seek($recepts, $i);
              $row = mysqli_fetch_row($recepts);
              echo "<h3>".$row[0]."</h3><br><b>Ингридиенты:</b> ".$row[1]."<br><b>Рецепт:</b><br>".$row[2]."<br>&nbsp<br>";
            }
          }
          else
          {echo "<p>Не найден ни один рецепт по запросу ".$_POST['s1']."</p>";}
          unset($_POST);
        }
      }
//работа с ЛИЧНЫМ КАБИНЕТОМ
      elseif ((($userdata['user_hash'] == $_COOKIE['hash']) or ($userdata['user_id'] == $_COOKIE['id']) or (($userdata['user_ip'] == $_SERVER['REMOTE_ADDR'])  and ($userdata['user_ip'] !== "0"))) && $_POST['autorizform']=="OK") { //если мы только авторизовались
        $login = htmlspecialchars($_POST['log']);
        $pas = htmlspecialchars($_POST['pas']);
        $result = mysqli_query ($link, "SELECT id, name FROM users WHERE login = '$login' AND password='$pas'");
        if (mysqli_num_rows($result) == 0)
        {unset($_POST); DIE("<p>Введен неверный логин или пароль! </p>");}
        else
        {
          $row = mysqli_fetch_row($result);
          //session_start();
          $_SESSION['authorized'] = 1;
          $_SESSION['id'] = $row[0];
          $_SESSION['username'] = $row[1];
          echo "<h2>Привет, ".$_SESSION['username']."!</h2><p>Здесь ты можешь изменить свои данные или добавить в базу новый рецепт.</p>
          <center><h3>Изменить данные</h3></center>
          <form action=\"index.php\" method=\"post\">
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
        <form action=\"index.php\" method=\"post\">
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
                Ингридиенты (пример оформления: \"Соль: 0.5 гр., Сахар: по вкусу\"):
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
        unset($_POST);
        }
      }
      elseif ((($userdata['user_hash'] == $_COOKIE['hash']) or ($userdata['user_id'] == $_COOKIE['id']) or (($userdata['user_ip'] == $_SERVER['REMOTE_ADDR'])  and ($userdata['user_ip'] !== "0"))) && $_POST['changelkform']=="OK") 
      { //если изменяем личные данные с формы в профиле
        $name = htmlspecialchars($_POST['namNEW']);
        $pas = htmlspecialchars($_POST['pasNEW']);
        $pas_r = htmlspecialchars($_POST['pas_rNEW']);
        $id = $_SESSION['id'];
        if (strlen($name)==0 && strlen($pas)==0)
        {unset($_POST); DIE("<p>Нечего изменять.</p>");}
        else {
          if (strlen($name)!=0 && preg_match("|^[a-zA-Zа-яА-Я_-\s]+$|", $name)==false)
            {unset($_POST); DIE("<p>В имени нельзя испольовать цифры и спецсимволы</p>");}
          if (strlen($pas)!=0 && !preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $pas))
            {unset($_POST); DIE("<p>Пароль должен содержать минимум 8 символов с использованием строчных и прописных латинских букв, цифр и спецсимволов. <br>(Нажмите кнопку \"Назад\" в браузере, чтобы попробовать снова.) </p>");}
          if ($pas != $pas_r)
            {unset($_POST); DIE("<p>Пароли не совпадают. <br>(Нажмите кнопку \"Назад\" в браузере, чтобы попробовать снова.)</p>");}
          if (strlen($name)!=0 && strlen($pas)!=0)
          {$result = mysqli_query ($link, "UPDATE users SET name='$name', password='$pas' WHERE id=$id"); $_SESSION['username']=$name;}
          elseif (strlen($name)!=0 && strlen($pas)==0)
          {$result = mysqli_query ($link, "UPDATE users SET name='$name' WHERE id=$id"); $_SESSION['username']=$name;}
          elseif (strlen($name)==0 && strlen($pas)!=0)
          {$result = mysqli_query ($link, "UPDATE users SET password='$pas' WHERE id=$id");}
          if ($result)
          {unset($_POST); DIE("<p>Данные успешно изменены.</p>");}
          else
          {unset($_POST); DIE("<p>Произошла странная ошибка. Данные не изменены. Попробуйте еще раз.</p>");}
        }
      }
      elseif ((($userdata['user_hash'] == $_COOKIE['hash']) or ($userdata['user_id'] == $_COOKIE['id']) or (($userdata['user_ip'] == $_SERVER['REMOTE_ADDR'])  and ($userdata['user_ip'] !== "0"))) && $_POST['exitform']=="OK") {  //если нажата кнопка Выход из профиля
        unset($_SESSION);
        $_SESSION['authorized'] = 0;
        unset($_POST);
        session_destroy();
        DIE("<h2>Вот вы и у нас!</h2><p>Не знаете что приготовить? Тогда данный сайт именно для Вас!</p><p>Выберете любой из видов поиска в меню слева и смело преступайте к работе!</p>");
      }
      elseif ((($userdata['user_hash'] == $_COOKIE['hash']) or ($userdata['user_id'] == $_COOKIE['id']) or (($userdata['user_ip'] == $_SERVER['REMOTE_ADDR'])  and ($userdata['user_ip'] == "0")))&& $_POST['addreceptform']=="OK") {  //если добавляем рецепт
        $name = htmlspecialchars($_POST['namRec']);
        $ingr = htmlspecialchars($_POST['ingrRec']);
        $rec = htmlspecialchars($_POST['recRec']);
        if (strlen($name)==0 || strlen($ingr)==0 || strlen($rec)==0)
        {unset($_POST); DIE("<p>При добавлении рецепта нужно заполнить все поля!</p>");}
        else
        {
          $ingr = str_replace("\n\r","<br>",$ingr);
          $ingr = str_replace(", ",", <br>",$ingr);
          $rec = str_replace("\n","<br>",$rec);
          $id = $_SESSION['id'];
          $isExist = mysqli_query ($link, "SELECT id FROM recepts WHERE name = '$name' AND ingr = '$ingr' AND rec_text = '$rec'");
          if (mysqli_num_rows($isExist) == 0)
          {
            $result = mysqli_query ($link, "INSERT INTO recepts (name,ingr,rec_text,id_user) VALUES ('$name','$ingr','$rec',$id)");
            if ($result)
            {unset($_POST); DIE("<p>Рецепт добавлен в базу!</p>");}
            else
            {unset($_POST); DIE("<p>Некая ошибка! Рецепт не добавлен в базу!</p>");}
          }
          else
          {
            unset($_POST); DIE("<p>Такой рецепт уже есть в базе!</p>");
          }
        }
      }
//конец работы с ЛИЧНЫМ КАБИНЕТОМ
    }
    else {
      echo "<h2>Вот вы и у нас!</h2>
      <p>Не знаете что приготовить? Тогда данный сайт именно для Вас!</p>
      <p>Выберете любой из видов поиска в меню слева и смело преступайте к работе!</p>";
    }
?>
</div>
</html>
