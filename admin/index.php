<?php

require_once '../config.php';
require_once '../functions.php';

session_start();

$dbh = connectDb();
$entries = array();
$sql = "select * from entries where status = 'active' order by created desc";

foreach ($dbh->query($sql) as $row) {
	array_push($entries, $row);
}

setToken();

//var_dump($entries); exit;
?>
<!DOCTYPE HTML>
<html lang="ja">
	<head>
		<meta charset="UTF-8">
		<title>お問い合わせ一覧</title>
		<style type="text/css">
			.deleteLink {
				color: blue;
				cursor: pointer;
				text-decoration: underline;
			}
		</style>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	</head>
	<body>
		<h1>データ一覧</h1>
		<p><span id="num"><?php echo count($entries); ?></span>件あります。</p>

		<ul>
			<?php foreach ($entries as $entry): ?>
			<li class="entry_<?php echo h($entry['id']); ?>">
				<?php echo h($entry['email']); ?>
				<a href="edit.php?id=<?php echo h($entry['id']); ?>">[編集]</a>
				<span class="deleteLink" data-id="<?php echo h($entry['id']); ?>">[削除]</span>
			</li>
			<?php endforeach; ?>
		</ul>
		<p><a href="<?php echo SITE_URL; ?>">お問い合わせフォームへ</a></p>
		<script type="text/javascript">
			$(function() {
				$('.deleteLink').click(function() {
					if (confirm('削除しても宜しいですか？')) {
						var num = $('#num').text();
						num--;

						$.post('./delete.php', {
							id: $(this).data('id')
							, token: '<?php echo h($_SESSION['token']); ?>'
						}, function(rs) {
							$('.entry_' + rs).fadeOut('slow');
							$('#num').text(num);
						});
					}
				});
			});
		</script>
	</body>
</html>