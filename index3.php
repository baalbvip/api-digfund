<?php


$exec = [];
if (isset($_POST['search'])) {

    print_r($_POST);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "http://localhost/api/get/statusaccount");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_POST, ['month' => $_POST['month'], 'year' => $_POST['year']]);
    $exec = json_decode(curl_exec($curl), true);

    print_r($exec);
}

error_reporting(0);

?>


<form method="POST" name="search">

    <select name="year" id="">
        <?php for ($i = 1; $i <= 12; $i++) { ?>
            <option value="<?= str_pad($i, 2, "0", STR_PAD_LEFT) ?>"><?= str_pad($i, 2, "0", STR_PAD_LEFT) ?></option>
        <?php } ?>
    </select>

    <select name="month" id="">
        <option value="2021">2021</option>
        <option value="2022">2022</option>
        <option value="2023">2023</option>
    </select>
    <button type="submit" name="search">Buscar</button>
</form>

<?php for ($i = 01; $i <= 12; $i++) { ?>
    <div className="">
        <p>Mes <?= $i ?></p>

        <?php $urlDownload = $exec[str_pad($i, 2, "0", STR_PAD_LEFT)][0]['url_download']; ?>

        <?php if (!empty($urlDownload)) { ?>
            <a href="<?= $urlDownload ?>" target="_blank">Descargar </a>

        <?php } else { ?>
            <a href="#" target="_blank">No hay archivos disponibles </a>

        <?php } ?>

    </div>

<?php } ?>