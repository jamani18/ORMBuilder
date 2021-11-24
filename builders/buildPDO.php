<?php
    require_once realpath('conf/conector.php');
?>


<?php if(isset($_GET['table'])): ?> 

    <?php 
    // recoge parametros de la url
    $lang = false;
    $tableName = strtolower($_GET['table']);
    $className = $_GET['class'];

    $classNameLower = lcfirst($className);


    //----------------------- consulta para recoger los campos de la tabla-----------------------------------------
    $sql = "SELECT column_name FROM information_schema.COLUMNS WHERE table_name LIKE '" . strtolower($tableName . "'");
    $sqlColumns = selectMultipleResults($sql);

    //--------array que va a contener todos los arrays--------------------
    $configObject = array('arrayId'=>array());

    //--------------Variables -------------
    $columNamesAttr = '';
    $columNamesRow = '';
    $columNamesGetter='';
    $columNamesLangUpdate = '';

    //check if table has lang field.
    foreach ($sqlColumns as $columnsNames) {
       if(explode('_', reset($columnsNames))[0] == 'es'){
           $lang = true;
           break;
       }
    }

    foreach ($sqlColumns as $columnsNames) {


    //--------------Array que separa el nombre de las columnas cuando contiene _ del lang-----------------------------------------------
        $arrayNameSeparateColums = explode('_', reset($columnsNames));

    //--------------if para insertar los nombres de las columnas en el ATRIBUTOS_PDO-------------------------------------------------
        $columNamesAttr .= reset($columnsNames) . ',';

    //----------------if para insertar los nombres de las columnas en el ATRIBUTOS_PDO con $Lang_--------------------------------------------------------------------------------
        $columNamesLangAttr = isset($arrayNameSeparateColums[1]) ? '$lang"."_' . $arrayNameSeparateColums[1] : reset($columnsNames);
        $configObject['arrayLangAttrFormat'][$columNamesLangAttr] = $columNamesLangAttr . ',';


    //----------------if para insertar los nombres de las columnas en el generateClassByRow-------------------------------------------------------------------------------
        $columNamesRow .= '$r["' . reset($columnsNames) . '"],';

    //----------------if para insertar los nombres de las columnas en el generateClassByRow  con $Lang_---------------------------------------------------------------------------   
        $columNamesLangRow = isset($arrayNameSeparateColums[1]) ? '$r[$lang.\'_' . $arrayNameSeparateColums[1] . '\']' : '$r[\'' . reset($columnsNames) . '\']';
        $configObject['arrayLangRowsFormat'][$columNamesLangRow] = $columNamesLangRow . ',';
    //----------------Insert los campos con get y atributo del create--------------------------------------------------------------------------------------------------------
        $columNamesGetter .=  '$' . $classNameLower. '->get' . ucfirst(reset($columnsNames)) . '()."\',\'". ';
    //----------------Insert los campos con get y atributo del create con $Lang_--------------------------------------------------------------------------------------------------------    
        $columNamesLangGetter = isset($arrayNameSeparateColums[1]) ? '.$sql' . ucfirst($arrayNameSeparateColums[1]) . '."\',\'" ' : '.$' . $classNameLower . '->get' . ucfirst(reset($columnsNames)) . '()."\',\'" ';
        $configObject['arrayLangGetterFormat'][$columNamesLangGetter] = $columNamesLangGetter;

    //----------------Campos del update si es con get o se utiliza el lang--------------------------------------------------------------------------------------------------------------------------------
        if (isset($arrayNameSeparateColums[1])) {
            $columNamesLangUpdate = '".$sql' . ucfirst($arrayNameSeparateColums[1]) . '."\',\' ';
        } elseif ($arrayNameSeparateColums[0] != 'id') {
            $columNamesLangUpdate = reset($columnsNames) . '=\'". $' . $classNameLower . '->get' . ucfirst(reset($columnsNames)) . '()."\', ';
        }
        $configObject['arrayLangUpdateFormat'][$columNamesLangUpdate] = $columNamesLangUpdate;
    //----------------saber todos los campos que empiezan por id menos el id de la propia tabla------------------------------------------------------------
        $findId = 'id';
        $pos = stripos(reset($columnsNames), $findId);

        $pos === false ? '' : (reset($columnsNames) != 'id' ? array_push($configObject['arrayId'], $columnsNames) : '');
    //---------------------------------------------------------------------------------------------------------------------   
        $columnsNamesLang = isset($arrayNameSeparateColums[1]) ? $arrayNameSeparateColums[1] : reset($columnsNames);
        $configObject['arrayLang'][$columnsNamesLang] = array(
            'name' => $columnsNamesLang,
            'lang' => isset($arrayNameSeparateColums[1]),
            'sql' => '$sql' . ucfirst($columnsNamesLang)
        );
        //endforeach
    }
    //----end php------
    ?>
    <html>
        <body>

            <p>&lt;?php </p>


            <p>require_once realpath('conf/conector.php');</p>

            <p>spl_autoload_register(function(){</p>
            <p>require_once realpath('class/<?= $className ?>.php');</p>

            <p>});</p>


            <!-- ATRIBUTOS -->
            <p>function ATRIBUTOS_<?= strtoupper($className) ?>PDO(<?= $lang ? '$lang=false' : '' ?>){</p>

    <?php if ($lang): ?>
                <p> $lang = $lang ?: getMyLanguage();</p>
                <p>return "<?= rtrim(implode($configObject['arrayLangAttrFormat']), ", ") ?>";</p>
            <?php else: ?>
                <p>return "<?= rtrim($columNamesAttr, ", ") ?>";</p>
    <?php endif; ?>
            <p>}</p>

            <!-- ATRIBUTOS CON ROW -->
            <p>function generate<?= $className ?>ClassByRow($r<?=$lang?',$lang=false':''?>){</p>
    <?php if ($lang): ?>
                <p>return new <?= $className ?>(<?= rtrim(implode($configObject['arrayLangRowsFormat']), ", ") ?>);</p>
    <?php else: ?>
                <p>return new <?= $className ?>(<?= rtrim($columNamesRow, ", ") ?>);</p>
            <?php endif; ?>

            <p>}</p>
            <!-- READALL -->
            <p>function read<?= $className ?>All(<?= $lang ? '$lang = false' : '' ?>): ?<?= $className ?>{</p>


            <p>$sql = "SELECT ".ATRIBUTOS_<?= strtoupper($className) ?>PDO()." FROM <?= $tableName ?>";</p>
            <p>$return = selectMultiple($sql,'generate<?= $className ?>ClassByRow','codigo'<?=$lang?',$lang':''?>);</p>

            <p>return $return?:null;</p>
            <p>}</p>

            <!-- READBYID -->
            <p>function read<?= $className ?>ById($id<?= $className ?><?= $lang ? ',$lang = false' : '' ?>): ?<?= $className ?>{</p>

            <p>$sql = "SELECT ".ATRIBUTOS_<?= strtoupper($className) ?>PDO()." FROM <?= $tableName ?> WHERE id='$id<?= $className ?>'";</p>
            <p>$return = selectSimple($sql,'generate<?= $className ?>ClassByRow'<?=$lang?',$lang':''?>);</p>

            <p>return $return?:null;</p>
            <p>}</p>

            <!-- READBYCODIGO -->
            <p>function read<?= $className ?>ByCodigo($cod<?= $className ?><?= $lang ? ',$lang = false' : '' ?>): ?<?= $className ?>{</p>

            <p>$sql = "SELECT ".ATRIBUTOS_<?= strtoupper($className) ?>PDO()." FROM <?= $tableName ?> WHERE codigo='$cod<?= $className ?>'";</p>
            <p>$return = selectSimple($sql,'generate<?= $className ?>ClassByRow'<?=$lang?',$lang':''?>);</p>

            <p>return $return?:null;</p>
            <p>}</p>
            <!-- READFORANEA -->
    <?php if (isset($arrayId)): ?>
        <?php foreach ($arrayId as $ids): ?>
                    <p>function read<?= $className ?>By<?= ucfirst(implode($ids)) ?>($<?= rtrim(implode($ids), ', ') ?>): ?<?= $className ?>{</p>

                    <p>$sql = "SELECT ".ATRIBUTOS_<?= strtoupper($className) ?>PDO()." FROM <?= $tableName ?> WHERE <?= implode($ids) ?>='$<?= implode($ids) ?>'";</p>
                    <p>$return = selectMultiple($sql,'generate<?= $className ?>ClassByRow','codigo'<?=$lang?',$lang':''?>);</p>

                    <p>return $return?:null;</p>
                    <p>}</p>
                    <p></p>
        <?php endforeach; ?>
    <?php endif; ?>


            <!-- CREATE -->
            <p>function create<?= $className ?>($<?= $classNameLower ?>) {</p>
    <?php if ($lang): ?>
                <P> $lang = getMyLanguage();</p>
                <P> $lenguajes = readLenguajeAll('sigla');</p>
                <?php foreach ($configObject['arrayLang'] as $value): ?>
                    <?php if ($value['lang']): ?>
                        <P>  $wordsTranslated<?= ucfirst($value['name']) ?> = translateCache($<?= $classNameLower ?>->get<?= ucfirst($value['name']) ?>(),$lang,$lenguajes);</p>
                        <P> <?= $value['sql'] ?> = getSqlLang($wordsTranslated<?= ucfirst($value['name']) ?>,false,true);</p>

                    <?php endif; ?>
        <?php endforeach; ?>


                <p>$sql = "INSERT INTO <?= strtolower($className) ?> VALUES('" <?= rtrim(implode($configObject['arrayLangGetterFormat']), "\"', '\" ") ?> "')";</p>

    <?php else: ?>
                <p>$sql = "INSERT INTO <?= strtolower($className) ?> VALUES('". <?= rtrim($columNamesGetter, "\"', '\". ") ?> ."')";</p>

            <?php endif; ?>

            <p>execSql($sql);</p>

            <p>return selectSimpleResult("SELECT id FROM <?= $tableName ?> WHERE codigo='".$<?= $classNameLower ?>->getCodigo()."'")['id'];</p>
            <p>}</p>

            <!-- REMOVE -->
            <p>function remove<?= $className ?>($id<?= $className ?>){</p>

            <p>$sql = "DELETE FROM <?= $tableName ?> WHERE id='$id<?= $className ?>'";</p>

            <p>execSql($sql);</p>
            <p>}</p>

            <!-- UPDATE -->
            <P>function update<?= $classNameLower ?>($<?= $classNameLower ?>){</p>

    <?php if ($lang): ?>
                <P> $lang = getMyLanguage();</p>
                <P> $lenguajes = readLenguajeAll('sigla');</p>
                <?php foreach ($configObject['arrayLang'] as $value): ?>
                    <?php if ($value['lang']): ?>
                        <P>  $wordsTranslated<?= ucfirst($value['name']) ?> = translateCache($<?= $classNameLower ?>->get<?= ucfirst($value['name']) ?>(),$lang,$lenguajes);</p>
                        <P> <?= $value['sql'] ?> = getSqlLang($wordsTranslated<?= ucfirst($value['name']) ?>,'<?= $value['name'] ?>');</p>

                    <?php endif; ?>
        <?php endforeach; ?>
                <p>$sql = "UPDATE <?= strtolower($className) ?> SET <?= rtrim(implode($configObject['arrayLangUpdateFormat']), "\"', '\". ") ?>." WHERE id = '".$<?= $classNameLower ?>->getId()."'";</p>

            <?php else: ?>
                <p>$sql = "UPDATE <?= strtolower($className) ?> SET <?= rtrim(implode($configObject['arrayLangUpdateFormat']), "\"', '\". ") ?> ." WHERE id = '".$<?= $classNameLower ?>->getId()."'";</p>

            <?php endif; ?>


            <P> execSql($sql);  </p>

            <P>}  </p>
        </body>
    </html>
    
<?php endif; ?>
