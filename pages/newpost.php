<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php if (!empty($_GET['edit'])): ?>
        <title>CodePlay :: Editar postagem</title>
    <?php else: ?>
        <title>CodePlay :: Nova postagem</title>
    <?php endif; ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../assets/css/global.css">

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
</head>

<body>
    <?php
        include("navbar.php");
        require(dirname(__FILE__) . "/../Controller/Post.php");
        include(dirname(__FILE__) . "/../Controller/Session.php");

        if (isset($_GET['success']) && $_GET['success']='1') {
            (new View("Sucesso!"))->success();
        }

        if(!(new Session())->loadSession()){
            header("location: ../pages/login.php");
        }

        $pass = true;
        $data = [
            ":post_title" => $_POST["title"] ?? null,
            ":post_content" => $_POST["description"] ?? null,
            ":language" => $_POST["language"] ?? null
        ];

        if (isset($_GET["edit"]) && !empty($_GET['edit'])) {
            $postdata = (new Post())->getPost($_GET['edit']);

            if ($_SESSION['id'] != $postdata['ID_user_FK'] && !isset($_SESSION['is_admin'])) {
                header("location: newpost.php");
            }

            $data[":ID_post"] = $_GET['edit'];
            $data["original_title"] = $postdata['post_title'];
            $data["edit"] = true;
        }

        $files = [
            "thumb" => $_FILES["thumb"] ?? null,
            "source" => $_FILES["source_files"] ?? null
        ];

        foreach (array_values($data) as $info){
            if (empty($info)) $pass = false;
        }

        if (!isset($data['edit'])){
            foreach (array_values($files) as $info){
                if (empty($info)) $pass = false;
            }
        }

        if ($pass){
            if (empty($files['thumb']['name']) && empty($files['source']['name'][0])) $files = null;

            if (!isset($data['edit'])) {
                if ((new Post())->createPost($data, $files)) {
                    header("location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]?success=1");
                }
            } else {
                if ((new Post())->updatePost($data, $files)) {
                    header("location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&success=1");
                }
            }
        }
    ?>
    
    <div id="main_form">
        <form id="post_form" method="POST" action="newpost.php<?= isset($data['edit']) ? '?edit='.$_GET["edit"] : '' ?>" enctype="multipart/form-data">
            <span class="information_btn" onclick="openModal('information')"><i class="bi bi-info-circle"></i></span>
            <div class="formpage">
                <div id="form_title">
                    <img id="imglogo" src="../assets/images/logo.png">
                    <p class="pixel large title">CodePlay</p>
                </div>
                
                <input class="input_7huy5 color" type="text" name="title"
                value="<?= $postdata['post_title'] ?? ''; ?>" placeholder="Título da postagem" required maxlength="60">
                
                <textarea id="editor" class="input_7huy5 color description" type="text" name="description"
                value="" placeholder="Fale sobre seu código" required><?= $postdata['post_content'] ?? ''; ?></textarea>

                <br>
                <div class="flex_block start">
                    <p class="color poppins">Linguagem utilizada:&nbsp;</p>
                    <select name="language" class="odin color myselect">
                        <option class="odin color" <?= (isset($postdata['language']) && $postdata['language'] == 'js') ? 'selected' : ''; ?> value="js">JS</option>
                        <option class="odin color" <?= (isset($postdata['language']) && $postdata['language'] == 'php') ? 'selected' : ''; ?> value="php">PHP</option>
                        <option class="odin color" <?= (isset($postdata['language']) && $postdata['language'] == 'c') ? 'selected' : ''; ?> value="c">C</option>
                        <option class="odin color" <?= (isset($postdata['language']) && $postdata['language'] == 'c++') ? 'selected' : ''; ?> value="c++">C++</option>
                        <option class="odin color" <?= (isset($postdata['language']) && $postdata['language'] == 'java') ? 'selected' : ''; ?> value="java">JAVA</option>
                    </select>
                </div>

                
                <div class="upload_buttons">
                    <div>
                        <label for="file" class="upload_btn">
                            <img title="Logo" id="src_files" class="upload_icons" src="../assets/images/file.png">
                            <p class="file_label color">Upload files!</p>
                        </label>
                        <input id="file" name="source_files[]" type="file" accept=".html, .css, .js, image/png, image/jpeg, image/jpg" hidden multiple onchange="
                            update_file_input('sources')
                        " <?= !isset($data['edit']) ? 'required' : ''; ?>/>
                    </div>
                    <div>
                        <label for="thumb" class="upload_btn">
                            <img class="upload_icons" id="thumb_logo" src="../assets/images/thumb.png">
                            <p class="file_label color" id="thumb_name">Upload thumb!</p>
                        </label>
                        <input id="thumb" name="thumb" type="file" accept="image/png, image/jpeg, image/jpg" hidden onchange="
                            update_file_input('thumb')
                        " <?= !isset($data['edit']) ? 'required' : ''; ?>/>
                    </div>
                </div>
                
                <button class="btn full" type="submit">POSTAR</button>
            </div>
            <div id='information'>
                <i onclick="closeModal('information')" class="information_btn bi bi-x-circle-fill"></i>
                <br>
                <p><i class="bi bi-asterisk"></i> Tamanhos de arquivos suportados:<br>
                &nbsp;&nbsp;&nbsp;&nbsp;Texto < 50kb<br>
                &nbsp;&nbsp;&nbsp;&nbsp;.png/.jpg/.jpeg < 1mb
                </p>
                <br>
                <hr>
                <br>
                <p><i class="bi bi-asterisk"></i> Jogos feitos em JAVASCRIPT devem ter um arquivo "index.html".</p>
                <br>
                <hr>
                <br>
                <p><i class="bi bi-asterisk"></i> Ao atualizar os arquivos da postagem, os arquivos antigos serão removidos!</p>
                <br>
                <hr>
                <br>
                <p><i class="bi bi-asterisk"></i> Dimensões de thumbnail recomendadas: 700x400</p>
            </div>
        </form>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        $('#editor').summernote({
        placeholder: 'Fale sobre seu código',
        tabsize: 2,
        height: 120,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
      });
    </script>
</body>

</html>