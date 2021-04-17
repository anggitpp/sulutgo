<?php
function getRow($sql)
{

    $res = db($sql);

    return $res ? mysql_fetch_assoc($res) : false;
}

function getRows($sql, $key = "")
{

    $res = db($sql);

    $index = -1;
    $result = [];

    while ($row = mysql_fetch_assoc($res)) {
        $index++;
        $result[!empty($key) ? $row[$key] : $index] = $row;
    }

    return $result;
}

function uploadXLS()
{

    global $s, $arrTitle, $path_import, $file_log;

    $file_name = $_FILES['file_import']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!empty($file_ext) && ($file_ext == "xls" || $file_ext == "xlsx")) {

        // static name, dont extravagant
        $file_name = customeUploadFile($_FILES['file_import'], md5($arrTitle[$s]), $path_import);

        if (empty($file_name)) {

            return json_encode([
                'message' => "Terjadi masalah file!",
                'result' => null
            ]);
        }

        if (file_exists($file_log))
            unlink($file_log);

        $log = fopen($file_log, "a+");

        fwrite($log, "UPLOADED \t: " . date("d/m/Y H:i:s") . "\n");
        fclose($log);

        return decodeXLS($file_name);
    }

    return json_encode([
        'message' => "file harus dalam format .xls atau .xlsx",
        'result' => null
    ]);
}

function decodeXLS($file_name)
{

    global $path_import, $file_log;

    require_once('plugins/PHPExcel/IOFactory.php');

    $log = fopen($file_log, "a+");

    try {

        $inputFileType = PHPExcel_IOFactory::identify($path_import . $file_name);
        $Reader = PHPExcel_IOFactory::createReader($inputFileType);
        $PHPExcel = $Reader->load($path_import . $file_name);

    } catch (Exception $e) {

        fwrite($log, "ERROR \t: " . date("d/m/Y H:i:s") . "Terjadi kesalahan sistem: " . $e->getMessage());
        fclose($log);

        if (file_exists($path_import . $file_name))
            unlink($path_import . $file_name);

        return json_encode([
            'message' => "Terjadi kesalahan Sistem: " . $e->getMessage() . ", file: $file_name",
            'return' => null
        ]);
    }

    if (file_exists($path_import . $file_name))
        unlink($path_import . $file_name);

    fwrite($log, "DECODED \t: " . date("d/m/Y H:i:s") . "\n\n");
    fclose($log);

    $sheet = $PHPExcel->getSheet(0);
    $sheet_size_y = $sheet->getHighestRow();

    $data = [];

    for ($n = 1; $n <= $sheet_size_y; $n++) {

        // AZ is last column record (conditional by data length)
        $row = $sheet->rangeToArray("A" . $n . ":AZ" . $n, null, true, true);
        $row = $row[0];

        if (!is_numeric($row[0])) continue;

        array_push($data, $row);
    }

    return json_encode([
        'message' => "Decoded",
        'result' => $data
    ]);
}

function customeUploadFile($file, $file_rename = "", $directory, $last_file = null)
{

    if (!empty($file['tmp_name'])) {

        if (!is_dir($directory)) mkdir($directory, 0755, true);

        $file_temp = $file['tmp_name'];
        $file_name = $file['name'];

        $extension = explode(".", $file_name);
        $file_renamed = empty($file_rename) ? $file_name : $file_rename . "." . end($extension);

        $file_renamed = str_replace("/", ".", $file_renamed);

        move_uploaded_file($file_temp, $directory . "/" . $file_renamed);

        return $file_renamed;
    }

    return $last_file;
}

function formImport($title)
{

    global $par, $file_template, $file_log;
    ?>

    <div class="pageheader">
        <h1 class="pagetitle"><?= $title ?></h1>
        <span class="pagedesc">&nbsp;</span>
    </div>

    <div id="contentwrapper" class="contentwrapper">
        <form class="stdform" id="form" onsubmit="return requestDecodeXLS('<?= getPar($par, 'mode') ?>');">

            <div id="loading">
                <div style="display: flex; width: 100%; height: 5rem; justify-content: center; align-items: center;">
                    <img src="styles/images/loaders/loader6.gif" height="30">
                </div>
            </div>

            <div id="form_input" class="subcontent">
                <p>
                    <label class="l-input-small">File</label>
                <div class="field">
                    <input type="text" id="file_name" class="input" maxlength="100" style="width:295px;"/>
                    <div class="fakeupload">
                        <input type="file" name="file_import" id="file_import" class="realupload" size="50" accept=".xls,.xlsx" onchange="jQuery('#file_name').val(this.value.replace('C:\\fakepath\\', ''))"/>
                    </div>
                </div>
                </p>
            </div>

            <div id="form_progress" class="progress" style="">
                <strong>Diproses</strong>
                <div class="bar2">
                    <div id="progress_bar" class="value orangebar" style="width: 0%;"></div>
                </div>
                <div style="display: flex; justify-content: end;">
                    <span id="progress_percentage">0/100 (0%)</span>
                </div>
            </div>

            <div  id="form_before">

                <div style="display: block; width: 100%; height: 1px; margin: 15px 0 10px 0; background-color: #ccc;"></div>

                <div style="display: flex">
                    <div style="flex: 1">
                        <a href="<?= $file_template ?>" target="_blank" class="detil">* download template.xlsx</a>
                    </div>
                    <input type="submit" name="_submit" class="btnSubmit radius2" value="Proses"/>
                </div>

            </div>

            <div  id="form_after">

                <div style="display: block; width: 100%; height: 1px; margin: 15px 0 10px 0; background-color: #ccc;"></div>

                <div style="display: flex">
                    <div style="flex: 1">
                        <a href="<?= $file_log ?>" class="btn btn1 btn_inboxi"><span>Download Log</span></a>
                    </div>
                    <input type="button" class="btnSubmit radius2" value="Selesai" onclick="closeBox(); parent.window.location='index.php?<?= getPar($par, "mode") ?>'"/>
                </div>

            </div>

            <br clear="all">

        </form>
    </div>

    <script>

        jQuery("#loading").hide()
        jQuery("#form_after").hide()
        jQuery("#form_progress").hide()


        // upload
        function requestDecodeXLS(_url) {

            jQuery("#loading").show()
            jQuery("#form_input").hide()
            jQuery("#form_before").hide()

            form = new FormData(jQuery("#form")[0])

            jQuery.ajax({
                type: "POST",
                url: 'ajax.php?par[mode]=decode' + _url,
                processData: false,
                contentType: false,
                enctype: 'multipart/form-data',
                data: form,
                success: (data) => {

                    result = JSON.parse(data)

                    if (result === false) {
                        alert("Terjadi kesalahan sistem, lihat: log");
                        return
                    }

                    if (result.result == null) {

                        alert(result.message)

                        jQuery("#loading").hide()
                        jQuery("#form_input").show()
                        jQuery("#form_before").show()

                        return
                    }

                    requestInsert(_url, result.result)
                }
            })

            return false
        }

        function requestInsert(_url, _datas, _position = 0) {

            // +1 for logging finish record PHP
            if (_position === (_datas.length + 1)) {

                jQuery("#progress h4").html("Selesai")

                jQuery("#progress_bar").removeClass("orangebar")
                jQuery("#progress_bar").addClass("bluebar")

                jQuery("#form_after").show()

                return
            }

            jQuery("#loading").hide()
            jQuery("#form_progress").show()

            precentage = Math.ceil((_position / _datas.length * 100))

            jQuery("#progress_percentage").html(_position + "/" + _datas.length + " (" + precentage + "%)")
            jQuery("#progress_bar").css("width", precentage + "%")

            jQuery.ajax({
                url: 'ajax.php?par[mode]=insert_import' + _url,
                type: "POST",
                data: {
                    'inp[size]': _datas.length,
                    'inp[position]': _position,
                    'inp[data]': JSON.stringify(_datas[_position])
                },
                success: (__data) => {
                    requestInsert(_url, _datas, _position + 1)
                }
            })

        }

    </script>

    <?php
}
