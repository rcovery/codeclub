<?php
	session_start();
	require_once(dirname(__FILE__) . "/../Controller/Post.php");
	require_once(dirname(__FILE__) . "/../Controller/User.php");
	require_once(dirname(__FILE__) . "/message.php");
?>

<!DOCTYPE html>
<html>
<head>
	<title>Codeplay :: Post</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="../assets/css/global.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/navbar.css">
</head>
<body>
	<?php include("navbar.php"); ?>

	<?php
		if (!isset($_GET["id"])) {
			(new View("Ocorreu um erro!"))->warning();
		} else {
			(new Post())->view($_GET["id"]);

			$result = (new Post())->getPost($_GET["id"]);
			$user = (new User())->getUser($result["ID_user_FK"]);
		}
	?>

	<div class="flex_block">
		<div class="main_page">
			<div class="block_image_78ajoe"><img id="game_thumb" src="<?= $result['post_files'] . "/thumb/thumbnail.dat" ?>"></div>
			<div class="game_infos">
				<div>
					<a class="postpage" target="_blank" href="profile.php?id=<?= $user['ID_user'] ?>"><img class="profile_pic postpage" src="<?= $user["pic_path"]; ?>"></a>
					<div>
						<p class="postpage info"><?= $result["post_title"] ?></p>
						<p class="postpage info"><?= $user["username"] ?></p>
					</div>
				</div>
				<div>
					<a href="<?= $result['post_files']; ?>" target="_blank" class="act_button" id="playgame"><span class="play_emoji">▶</span> JOGAR</a>
					<?php if($_SESSION['id'] == $user['ID_user']): ?>
						<a class="act_button" id="editgame" href="newpost.php?edit=<?= $result['ID_post']; ?>" target="_blank"><span class="play_emoji"></span>EDIT</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<script src="../assets/js/script.js"></script>
</body>
</html>