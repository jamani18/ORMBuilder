<?php

require_once realpath('conf/Sqlconnector.php');

?>

<?php if(isset($_GET['table'])): ?> 

    <?php
    // recoge parametros de la url

    // consulta para recoger los campos de la tabla
        $sql = "SELECT column_name FROM information_schema.COLUMNS WHERE table_name LIKE '" . strtolower($_GET['table'] . "'");
        $arrayResultColumns = selectMultipleResults($sql);

    //---------Variables--------------------------------------
    $constructorParam = '';
    $constructorAttr = '';
    $getterValues ='';



    //--------------------------------------------------------
    foreach($arrayResultColumns as $resultRow){
    //--------------Array que separa el nombre de las columnas cuando contiene _ del lang-----------------------------------------------
        $arrayNameSeparateColum = explode('_', reset($resultRow));
    //------------------------------------------------------------------------------------------    
        $attrNameClass = isset($arrayNameSeparateColum[1]) ? $arrayNameSeparateColum[1] : reset($resultRow);
       $arrayDatos['arrayAttrClass'][$attrNameClass] = $attrNameClass;

    }
    //------------Fin Foreach-----------------------------------

    //---------------------------------------------------
    foreach ($arrayDatos['arrayAttrClass'] as $attrName) {
        $dividedFieldsById = explode('id', $attrName);
    //--------sacar las atributos que hacen referencia a clase con null--------------       
        $attrNullClass = isset($dividedFieldsById[1]) ? lcfirst($dividedFieldsById[1]) : '';
        $arrayDatos['arrayNullClass'][$attrNullClass] = $attrNullClass;

    //---Parametros para el constructor
        $constructorParam .= '$' . $attrName . ',';
    //---Campos para el constructor
        $constructorAttr .= '$this->' . $attrName . ' = $' . $attrName . ';<br>';
    //---Campos con Getter--------- 
        $getterValues .= 'public function get' . ucfirst($attrName) . '(){<br> return $this->' . $attrName . ';<br>}<br>';
    //---Campos con Setter---------------
        $setterValues = isset($dividedFieldsById[1]) ? ($dividedFieldsById[1] === '' ? 'public function setId' . $dividedFieldsById[1] . '($id' . $dividedFieldsById[1] . '): void {<br> $this->id' . $dividedFieldsById[1] . ' = $id' . $dividedFieldsById[1] . ';<br>}<br>' : 'public function setId' . $dividedFieldsById[1] . '($id' . $dividedFieldsById[1] . '): void {<br> $this->id' . $dividedFieldsById[1] . ' = $id' . $dividedFieldsById[1] . ';<br> $this->'.$dividedFieldsById[1].' = NULL;<br>}<br>').'' : 'public function set' . ucfirst($attrName) . '($' . $attrName . '): void {<br> $this->' . $attrName . ' = $' . $attrName . ';<br>}<br>';
        $arrayDatos['arraySetter'][$setterValues] = $setterValues;

    }
    //------------Fin Foreach---------------------------
        ?>
        <html>
            <body>
        <p>&lt;?php </p>

                <!-- Clase -->
                <p>class <?= $_GET['class'] ?> {</p>
                <!-- Atributos -->
        <?php foreach ($arrayDatos['arrayAttrClass'] as $resultRow) : ?>
                    protected $<?= $resultRow; ?>;<br>
        <?php endforeach; ?>
                <p></p>
                <!-- Atributos nulos -->
        <?php foreach ($arrayDatos['arrayNullClass'] as $attrObjNull) : ?>
                    <?php if ($attrObjNull != ''): ?>
                        protected $<?= $attrObjNull ?>= NULL;<br>
                    <?php endif; ?>
                <?php endforeach; ?>

                <p></p>
                <!-- Constructor -->
                public function __construct(<?= rtrim($constructorParam, ', ') ?>) {
                <p></p>
                <?= $constructorAttr ?>


                }
                <p></p>
        <?= $getterValues ?>
                <?php foreach ($arrayDatos['arraySetter'] as $set) : ?>
                    <?= $set ?>  

        <?php endforeach; ?>


                <?php foreach ($arrayDatos['arrayNullClass'] as $attrObjNull) : ?>
                    <?php if ($attrObjNull != ''): ?>
                        public function get<?= ucfirst($attrObjNull) ?>() ?: <?= ucfirst($attrObjNull) ?> {<br>
                        $this-><?= $attrObjNull ?> === NULL ? $this-><?= $attrObjNull ?> = read<?= ucfirst($attrObjNull) ?>byId($this->id<?= ucfirst($attrObjNull) ?>) : false;<br>
                        return $this-><?= $attrObjNull ?>?:null;<br>
                        <p>}</p>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php foreach ($arrayDatos['arrayNullClass'] as $attrObjNull) : ?>
            <?php if ($attrObjNull != ''): ?>
                        public function set<?= ucfirst($attrObjNull) ?>($<?= $attrObjNull ?>){<br>
                        $this-><?= $attrObjNull ?> = $<?= $attrObjNull ?>; <br>
                        $this->id<?= ucfirst($attrObjNull) ?> = $<?= $attrObjNull ?>->getId();<br>
                        <p>}</p>
                    <?php endif; ?>
                <?php endforeach; ?>

            <p>}</p>
        </body>
    </html>

<?php endif; ?>
