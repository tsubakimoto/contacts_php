<?php
require_once '../config.php';
require_once '../functions.php';

session_start();

$dbh = connectDb();

if (preg_match('/^[1-9][0-9]*$/', $_GET['id'])) {
	$id = (int)$_GET['id'];
} else {
	echo '不正なIDです！';
	exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	// 投稿前
	setToken();

	$stmt = $dbh->prepare('select * from entries where id = :id limit 1');
	$stmt->execute(array(':id'=>$id));
	$entry = $stmt->fetch() or die('no one found!');

	$name = $entry['name'];
	$email = $entry['email'];
	$memo = $entry['memo'];

} else {
	// 投稿後
	checkToken();

	$name = $_POST['name'];
	$email = $_POST['email'];
	$memo = $_POST['memo'];

	$error = array();

	// エラー処理
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$error['email'] = 'メールアドレスの形式が正しくありません';
	}
	if (empty($email)) {
		$error['email'] = 'メールアドレスを入力してください';
	}
	if (empty($memo)) {
		$error['memo'] = '内容を入力してください';
	}

	if (empty($error)) {
		// DB更新
		$sql = 'update entries set
					name = :name
					, email = :email
					, memo = :memo
					, modified = now()
				where id = :id';
		$stmt = $dbh->prepare($sql);
		
		$params = array(
			':name' => $name
			, ':email' => $email
			, ':memo' => $memo
			, ':id' => $id
		);
		$stmt->execute($params);

		// ありがとうページヘ
		header('Location: ' . ADMIN_URL);
		exit;
	}
}
?>
<!DOCTYPE HTML>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title>編集：お問い合わせフォーム</title>
	</head>
	<body>
		<h1>データの編集</h1>
		<form action="" method="post">
			<p>
				お名前：<input type="text" name="name" value="<?php echo h($name); ?>" />
			</p>
			<p>
				メールアドレス*：<input type="text" name="email" value="<?php echo h($email); ?>" />
<?php if ($error['email']) {
	echo h($error['email']);
} ?>
			</p>
			<p>内容*：</p>
			<p>
				<textarea name="memo" cols="48" rows="5"><?php echo h($memo); ?></textarea>
			</p>
			<?php if ($error['memo']) {
				echo h($error['memo']);
			} ?>

			<p><input type="submit" value="更新" /></p>
			<input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>" />
		</form>
		<p><a href="<?php echo ADMIN_URL; ?>">戻る</a></p>
	</body>
</html>