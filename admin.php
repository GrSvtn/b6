<head>
    <title>Задание 6</title>
    <link rel="stylesheet" href="styleadmin.css">
    <meta name="viewport" content="width=device-width initial-scale=1">
</head>
<body>
<?php
/**
 * Задача 6. Реализовать вход администратора с использованием
 * HTTP-авторизации для просмотра и удаления результатов.
 **/

// функция вызывающая окошко http аутентификации
function authorize()
{
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Вы должны авторизоваться</h1>');
    exit();
}

//
if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
    authorize();
}
$user = 'u53890';
$pass = '8091112';
$db = new PDO('mysql:host=localhost;dbname=u53890', $user, $pass, [PDO::ATTR_PERSISTENT => true]);
$stmt = $db->prepare("SELECT * FROM Admin where login = ? && hash_pass = ?");
$stmt->execute([$_SERVER['PHP_AUTH_USER'], md5($_SERVER['PHP_AUTH_PW'])]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$admin) {
    authorize();
}

if ($_SERVER['REQUEST_METHOD'] == "GET") {
if (!empty($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM form WHERE id=?");
    $stmtErr = $stmt->execute([$_GET['delete']]);
    header('Location: ./admin.php');
}
if (!empty($_GET['change'])) {
$stmt = $db->prepare("SELECT * FROM form WHERE id=?");
$stmtErr = $stmt->execute([$_GET['change']]);
$user = $stmt->fetch();

$stmt = $db->prepare("SELECT power FROM powers WHERE id IN (SELECT power_id FROM form_power WHERE form_id = ?)");
$stmt->execute([$_GET['change']]);
$powers = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $powers[$row['power']] = $row['power'];
}
$stmt = $db->prepare("SELECT power FROM powers");
$stmt->execute();
$abilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
setcookie('changed_uid', $user['id'], time() + 30 * 24 * 60 * 60);
?>
<p>Изменение данных пользователя №<?php print ($user['id']); ?></p>
<form action="" method="POST">
    <label>
        Имя:<br>
        <input name="name"
               placeholder="name" required <?php print('value="' . $user['name'] . '"'); ?>>
    </label><br>

    <label>
        E-mail:<br>
        <input name="email"
               type="email"
               placeholder="email" required <?php print('value="' . $user['email'] . '"'); ?>>
    </label><br>

    <label>
        дата рождения:
        <input class="form" name="birthday"
               value="<?php print($user['birthday']); ?>"
               type="date"/>
    </label><br/>

    Пол: <br>
    <label><input type="radio"
                  name="sex" value="Man" required <?php if ($user['sex'] == 'Man') {
            print 'checked';
        } ?>>
        Мужской</label>
    <label><input type="radio"
                  name="sex" value="Female"
                  required <?php if ($user['sex'] == 'Female') {
            print 'checked';
        } ?>>
        Женский</label><br>

    Количество: <br>
    <label><input type="radio"
                  name="limbs" value="1"
                  required <?php if ($user['limbs'] == '1') {
            print 'checked';
        } ?>>
        1</label>
    <label><input type="radio"
                  name="limbs" value="2"
                  required <?php if ($user['limbs'] == '2') {
            print 'checked';
        } ?>>
        2</label>
    <label><input type="radio"
                  name="limbs" value="3"
                  required <?php if ($user['limbs'] == '3') {
            print 'checked';
        } ?>>
        3</label>
    <label><input type="radio"
                  name="limbs" value="4"
                  required <?php if ($user['limbs'] == '4') {
            print 'checked';
        } ?>>
        4</label><br>
    <label><input type="radio"
                  name="limbs" value="5"
                  required <?php if ($user['limbs'] == '5') {
            print 'checked';
        } ?>>
        5</label><br>

    <label>
        Сверхспособности:
        <br>
        <select name="powers[]" multiple="multiple" required>
            <?php
            foreach ($abilities as $ability){
                $selected = empty($powers[$ability['power']]) ? '' : 'selected';
                printf('<option value="%s" %s>%s</option>', $ability['power'], $selected, $ability['power']);
            }
            ?>
        </select>
    </label><br>

    <label>
        Биография:<br>
        <textarea name="bio"><?php print($user['bio']); ?></textarea>
    </label><br>

    <input type="submit" value="Отправить">
</form>
<?php
exit();
}
print('Вы успешно авторизовались и видите защищенные паролем данные.');

$stmt = $db->prepare("SELECT * FROM form");
$stmtErr = $stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT power FROM powers WHERE id IN (SELECT power_id FROM form_power WHERE form_id = ?)");


print ('<table>
	<thead>
		<tr>
			<td>ID</td>
			<td>Имя</td>
			<td>Почта</td>
			<td>Год рождения</td> 
			<td>Пол</td>
			<td>Количество конечностей</td>
			<td>Биография</td>
			<td>Способности</td>
			<td>Удалить</td>
			<td>Изменить</td>
		</tr>
	</thead>
	<tbody>');

foreach ($result as $user) {
    print ('<tr>');
    foreach ($user as $key => $value) {
        print('<td>' . $value . '</td>');
    }
    print ('<td>');
    $stmtErr = $stmt->execute([$user['id']]);
    $powers = $stmt->fetchAll();
    foreach ($powers as $power) {
        print $power['power'] . " " ;
    }
    print ('</td>');
    print ('<td><a href="./admin.php?delete=' . $user['id'] . '">Удалить</a></td>');
    print ('<td><a href="./admin.php?change=' . $user['id'] . '">Изменить</a></td>');
    print ('</tr>');
}
print ('</tbody>
    </table>');

$stmt = $db->prepare("SELECT COUNT(1), power_id FROM form_power GROUP BY power_id");
$stmt->execute();
$statistics = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->prepare("SELECT power FROM powers where id = ?");
foreach ($statistics as $statistic) {
    print ('<p>' . $statistic['COUNT(1)'] . ' человек обладают ');
    $stmtErr = $stmt->execute([$statistic['power_id']]);
    $power = $stmt->fetch();
    print $power['power'] . '</p>';
}
} else {
    $stmt = $db->prepare("UPDATE form SET name= :name, email= :email, birthday= :birthday, sex= :sex, limbs= :limbs, bio= :bio where id = :id");
    $stmtErr = $stmt->execute(['id' => $_COOKIE['changed_uid'], 'name' => $_POST['name'], 'email' => $_POST['email'], 'birthday' => $_POST['birthday'], 'sex' => $_POST['sex'], 'limbs' => $_POST['limbs'], 'bio' => $_POST['bio']]);
    setcookie('changed_uid', '', 1);

    $stmt = $db->prepare("DELETE FROM form_power WHERE form_id=?");
    $stmtErr = $stmt->execute([$_COOKIE['changed_uid']]);

    $stmt2 = $db->prepare("INSERT INTO form_power (form_id, power_id) VALUES (:form_id, (SELECT id FROM powers WHERE power=:power))");
    foreach ($_POST['powers'] as $power) {
        $stmt2->bindParam(':form_id', $_COOKIE['changed_uid']);
        $stmt2->bindParam(':power', $power);
        $stmt2->execute();
    }

    header('Location: admin.php');
}