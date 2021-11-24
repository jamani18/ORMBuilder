<?php ?>

<html>
    <style>

        main{
            display: flex;
            flex-direction: row;
            height: 100%;
        } 
        main > article{
            cursor: pointer;
            padding: 0 18px;
            height: 93%;
            overflow: auto;
            border: 1px solid #aeaeae;
            margin: 0 11px;
            width: 41%;
            border-radius: 4px;
        }
        main > article.form{
            width: 20%;
            cursor: auto;
            padding-top: 15px;
            box-sizing: border-box;
        }
            main > article.form .field{
                margin-bottom: 15px;
            }
        main > article > label{
            font-weight: bold;
            padding: 12px 0px;
            display: block;
            border-bottom: 1px solid #b0aeae;
        }

    </style>
    <script type="text/javascript">

        window.onload = function () {

            function selectText(containerid) {
                if (document.selection) { // IE
                    var range = document.body.createTextRange();
                    range.moveToElementText(document.getElementById(containerid));
                    range.select();
                } else if (window.getSelection) {
                    var range = document.createRange();
                    range.selectNode(document.getElementById(containerid));
                    window.getSelection().removeAllRanges();
                    window.getSelection().addRange(range);
                }
            }

            document.getElementById('pdoTable').onclick = function () {
                selectText('pdoCode');
                document.getElementById('pdoTable').setAttribute('style', 'background-color: #e6e6e6');
                document.getElementById('classTable').setAttribute('style', 'background-color: white');
            }

            document.getElementById('classTable').onclick = function () {
                selectText('classCode');
                document.getElementById('classTable').setAttribute('style', 'background-color: #e6e6e6');
                document.getElementById('pdoTable').setAttribute('style', 'background-color: white');
            }
        }


    </script>
    <body>
        <main>
            
            <article id="form" class="form">
                <form id="formData">
                    <div class="field">
                        <label>Class name</label>
                        <input name="class" type="text">
                    </div>
                    
                    <div class="field">
                        <label>Table name</label>
                        <input name="table" type="text">
                    </div>
                    
                    
                    <input type="submit" name="send" value="Build code" action='classPdoBuilder.php'/>
                </form>
                
                    
             
            </article>
            
            <?php if(isset($_GET['table'])): ?> 
                <article id="classTable">
                    <label>Class</label>
                    <div id="classCode">
    <?php include_once 'etc/builders/buildClass.php'; ?>
                    </div>
                </article>
                <article id="pdoTable">
                    <label>PDO</label>
                    <div id="pdoCode">
    <?php include_once 'etc/builders/buildPDO.php'; ?>
                    </div>
                </article>
            <?php endif; ?>
        </main>

    </body>
</html>
