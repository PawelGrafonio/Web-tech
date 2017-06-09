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
    //dsn для использования PDO
    $dsn = "mysql:dbname=site;host=127.0.0.1;charset=utf8_general_ci";
    $opt = array(
    'PDO::ATTR_ERRMODE' => PDO::ERRMODE_EXCEPTION,
    'PDO::ATTR_DEFAULT_FETCH_MODE' => PDO::FETCH_ASSOC,
    'PDO::ATTR_EMULATE_PREPARES' => false,
    );
    $pdo = new PDO($dsn, 'root', '', $opt);
    if (count($_POST) > 0) //если пришли после какой-то формы
    {
      if(mb_strlen($_POST['logR'])!==0 && mb_strlen($_POST['namR'])!==0 && mb_strlen($_POST['pasR'])!==0 && mb_strlen($_POST['pas_rR'])!==0) //если с формы регистрации
      {
        //переменные с формы
        $args = array(
          'logR' =>  FILTER_SANITIZE_STRING,
          'namR'  =>  FILTER_SANITIZE_STRING,
          'pasR'   =>  FILTER_SANITIZE_STRING,
          'pas_rR' =>  FILTER_SANITIZE_STRING,
        );
        $data = filter_input_array(INPUT_POST, $args);
        # проверям логин
        if(!preg_match("/^[a-zA-Z0-9]+$/",$data['logR']))
        {
            $err[] = "Логин может состоять только из букв английского алфавита и цифр";
        }
        if(!preg_match("/^[a-zA-Z0-9]+$/",$data['namR']))
        {
            $err[] = "имя может состоять только из букв английского алфавита и цифр";
        }
        if(mb_strlen($data['logR']) < 3 || mb_strlen($data['logR']) > 30)
        {
            $err[] = "Логин должен быть не меньше 3-х символов и не больше 30";
        }
        # проверяем пароль
        if (!preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $data['pasR']))
        {
          $err[] = "Пароль должен содержать минимум 8 символов с использованием строчных и прописных латинских букв, цифр и спецсимволов";
        }
        if ($data['pasR'] !== $data['pas_rR'])
        {
          $err[] = "Пароли не совпадают";
        }
        # проверяем, не сущестует ли пользователя с таким именем
        $stmt = $pdo->prepare("SELECT COUNT(id) as x FROM users WHERE login= ?");
        $stmtData = array($data['logR']);  //потому что иначе возникает Warning с тем, что в execute должен быть массив
        $stmt->execute($stmtData);
        while ($row = $stmt->fetch(PDO::FETCH_LAZY))
        {
            if ($row['x']!=='0')
            $err[] = "Пользователь с таким логином уже существует в базе данных";
        }
        # Если нет ошибок, то добавляем в БД нового пользователя
        if(count($err) === 0)
        {
          $stmt = $pdo->prepare("INSERT INTO users (login,name,password) VALUES ( ?, ?, ?)");
          $stmtData = array($data['logR'], $data['namR'], sha1($data['pasR']));
          $stmt->execute($stmtData);
          $insRowCount = $stmt->rowCount();
          if ($insRowCount !== 0)
          {die("<p>Вы успешно зарегистрировались! <br>Перейдите на вкладку Профиль, чтобы войти.</p>");}
          else
          {die("<p>Произошла странная ошибка. Регистрация не удалась. Попробуйте еще раз.</p>");}
        }
        else
        {
          print "<b>При регистрации произошли следующие ошибки:</b><br>";
          echo implode('<br>', $err);
        }
      }
      elseif ($_POST['search_fform']==="OK") { //если с формы поиска по блюду
        if (mb_strlen($_POST['s2'])===0)
        {die("<p>Не введены данные для поиска!</p>");}
        else
        {
          $name = filter_input(INPUT_POST, 's2', FILTER_SANITIZE_STRING);
          $name1 = "%{$name}%";
          $stmt = $pdo->prepare("SELECT name, ingr, rec_text FROM recepts WHERE name like ?");
          $stmtData = array($name1);
          $stmt->execute($stmtData);
          while ($row = $stmt->fetch(PDO::FETCH_LAZY))
          {
              $recAr[] = "<h3>{$row[0]}</h3><br><b>Ингридиенты:</b> {$row[1]}<br><b>Рецепт:</b><br>{$row[2]}<br>&nbsp<br>";
          }
          if (count($recAr) !== 0)
          {echo "<center><h3>Рецепты по запросу {$name}</h3></center><br>"; echo implode('',$recAr);}
          else
          {echo "<p>Не найден ни один рецепт по запросу {$name}</p>";}
        }
      }
      elseif ($_POST['search_iform']=="OK") { //если с формы поиска по ингридиентам
        if (mb_strlen($_POST['s1'])===0)
        {die("<p>Не введены данные для поиска!</p>");}
        else
        {
          $name = filter_input(INPUT_POST, 's1', FILTER_SANITIZE_STRING);
          $ingres = explode(", ", $name);
          $query = "SELECT name, ingr, rec_text FROM recepts WHERE 1";
          for ($i=0; $i < count($ingres) ; $i++)
          {
            $ingres[$i] = "%{$ingres[$i]}%";
            $query = $query . " AND ingr like ?";
          }
          $stmt = $pdo->prepare($query);
          $stmt->execute($ingres);
          while ($row = $stmt->fetch(PDO::FETCH_LAZY))
          {
              $recAr[] = "<h3>{$row[0]}</h3><br><b>Ингридиенты:</b> {$row[1]}<br><b>Рецепт:</b><br>{$row[2]}<br>&nbsp<br>";
          }
          if (count($recAr) !== 0)
          {echo "<center><h3>Рецепты по запросу {$name}</h3></center><br>"; echo implode('',$recAr);}
          else
          {echo "<p>Не найден ни один рецепт по запросу {$name}</p>";}
        }
      }
//работа с ЛИЧНЫМ КАБИНЕТОМ
      elseif ((($userdata['user_hash'] === $_COOKIE['hash']) || ($userdata['user_id'] === $_COOKIE['id']) || (($userdata['user_ip'] === $_SERVER['REMOTE_ADDR'])  && ($userdata['user_ip'] !== "0"))) && $_POST['autorizform']==="OK") { //если мы только авторизовались
        $login = filter_input(INPUT_POST, 'log', FILTER_SANITIZE_STRING);
        $pas = filter_input(INPUT_POST, 'pas', FILTER_SANITIZE_STRING);
        $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE login = ?"); //получаем пароль по введенному логину
        $stmtData = array($login);
        $stmt->execute($stmtData);
        while ($row = $stmt->fetch(PDO::FETCH_LAZY))
        {
            if ($row['password']!==sha1($pas)) //сравниваем полученный из базы пароль и введенный пользователем
            {die("<p>Введен неверный логин или пароль! </p>");}
            else
            {$id=$row[0]; $name = $row[1];}
        }
        $_SESSION['authorized'] = 1;
        $_SESSION['id'] = $id;
        $_SESSION['username'] = $name;
        echo "<h2>Привет, {$_SESSION['username']}!</h2><p>Здесь ты можешь изменить свои данные или добавить в базу новый рецепт.</p>
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
      }
      elseif ((($userdata['user_hash'] === $_COOKIE['hash']) || ($userdata['user_id'] === $_COOKIE['id']) || (($userdata['user_ip'] === $_SERVER['REMOTE_ADDR'])  && ($userdata['user_ip'] !== "0"))) && $_POST['changelkform']==="OK")
      { //если изменяем личные данные с формы в профиле
        $name = filter_input(INPUT_POST, 'namNEW', FILTER_SANITIZE_STRING);
        $pas = filter_input(INPUT_POST, 'pasNEW', FILTER_SANITIZE_STRING);
        $pas_r = filter_input(INPUT_POST, 'pas_rNEW', FILTER_SANITIZE_STRING);
        $id = $_SESSION['id'];
        if (mb_strlen($name)===0 && mb_strlen($pas)===0)
        {die("<p>Нечего изменять.</p>");}
        else {
          if (mb_strlen($name)!==0 && !preg_match("|^[a-zA-Zа-яА-Я_-\s]+$|", $name))
            {die("<p>В имени нельзя испольовать цифры и спецсимволы</p>");}
          if (mb_strlen($pas)!==0 && !preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $pas))
            {die("<p>Пароль должен содержать минимум 8 символов с использованием строчных и прописных латинских букв, цифр и спецсимволов. <br>(Нажмите кнопку \"Назад\" в браузере, чтобы попробовать снова.) </p>");}
          if ($pas !== $pas_r)
            {die("<p>Пароли не совпадают. <br>(Нажмите кнопку \"Назад\" в браузере, чтобы попробовать снова.)</p>");}
          $insRowCount = 0;
          if (mb_strlen($name)!==0 && mb_strlen($pas)!==0)
          {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
            $stmtData = array($name, sha1($pas), $id);
            $stmt->execute($stmtData);
            $insRowCount = $stmt->rowCount();
            $_SESSION['username']=$name;
          }
          elseif (strlen($name)!=0 && strlen($pas)==0)
          {
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $stmtData = array($name, $id);
            $stmt->execute($stmtData);
            $insRowCount = $stmt->rowCount();
            $_SESSION['username']=$name;
          }
          elseif (strlen($name)==0 && strlen($pas)!=0)
          {
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmtData = array(sha1($pas), $id);
            $stmt->execute($stmtData);
            $insRowCount = $stmt->rowCount();
          }
          if ($insRowCount !== 0)
          {die("<p>Данные успешно изменены.</p>");}
          else
          {die("<p>Произошла странная ошибка. Данные не изменены. Попробуйте еще раз.</p>");}
        }
      }
      elseif ((($userdata['user_hash'] === $_COOKIE['hash']) || ($userdata['user_id'] === $_COOKIE['id']) || (($userdata['user_ip'] === $_SERVER['REMOTE_ADDR'])  && ($userdata['user_ip'] !== "0"))) && $_POST['exitform']==="OK") {  //если нажата кнопка Выход из профиля
        unset($_SESSION);
        $_SESSION['authorized'] = 0;
        session_destroy();
        die("<h2>Вот вы и у нас!</h2><p>Не знаете что приготовить? Тогда данный сайт именно для Вас!</p><p>Выберете любой из видов поиска в меню слева и смело преступайте к работе!</p>");
      }
      elseif ((($userdata['user_hash'] == $_COOKIE['hash']) or ($userdata['user_id'] == $_COOKIE['id']) or (($userdata['user_ip'] == $_SERVER['REMOTE_ADDR'])  and ($userdata['user_ip'] == "0")))&& $_POST['addreceptform']=="OK") {  //если добавляем рецепт
        $name = filter_input(INPUT_POST, 'namRec', FILTER_SANITIZE_STRING);
        $ingr = filter_input(INPUT_POST, 'ingrRec', FILTER_SANITIZE_STRING);
        $rec = filter_input(INPUT_POST, 'recRec', FILTER_SANITIZE_STRING);
        if (mb_strlen($name)===0 || mb_strlen($ingr)===0 || mb_strlen($rec)===0)
        {die("<p>При добавлении рецепта нужно заполнить все поля!</p>");}
        else
        {
          $ingr = str_replace("\n\r","<br>",$ingr);
          $ingr = str_replace(", ",", <br>",$ingr);
          $rec = str_replace("\n","<br>",$rec);
          $id = $_SESSION['id'];
          $stmt = $pdo->prepare("SELECT COUNT(id) FROM recepts WHERE name = ? AND ingr = ? AND rec_text = ?");
          $stmtData = array($name, $ingr, $rec);
          $stmt->execute($stmtData);
          while ($row = $stmt->fetch(PDO::FETCH_LAZY))
          {
              if ($row['COUNT(id)']!=='0')
              die("<p>Такой рецепт уже есть в базе!</p>");
          }
          $stmt = $pdo->prepare("INSERT INTO recepts (name,ingr,rec_text,id_user) VALUES ( ?, ?, ?, ?)");
          $stmtData = array($name, $ingr, $rec, $id);
          $stmt->execute($stmtData);
          $insRowCount = $stmt->rowCount();
          if ($insRowCount !== 0)
          {die("<p>Рецепт добавлен в базу!</p>");}
          else
          {die("<p>Некая ошибка! Рецепт не добавлен в базу!</p>");}
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
