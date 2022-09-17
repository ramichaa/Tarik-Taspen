<?php

include("inc/fungsi.php");
include("class/taspen.class.php");
require 'vendor/autoload.php';

$consoleColor = new PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor();

echo PHP_EOL;
echo PHP_EOL;

echo $consoleColor->apply("color_10", "MENARIK DATA SPT TAHUNAN WP PENSIUNAN DARI WEB TASPEN") . PHP_EOL;
echo '=======================' . PHP_EOL;

$reader_no_taspen = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
$reader_no_taspen->setReadDataOnly(true);
$reader_no_taspen->setReadEmptyCells(false);
$spreadsheet_no_taspen = $reader_no_taspen->load('input/npwp_taspen.xlsx');
$sheet_no_taspen = $spreadsheet_no_taspen->getSheetByName('npwp');

$max_row_no_taspen = $sheet_no_taspen->getHighestRow();

$list_no_taspen = $sheet_no_taspen->rangeToArray('A2:A' . $max_row_no_taspen);

$tahun = date('Y') - 1;

$count_no_taspen = count($list_no_taspen);

if ($count_no_taspen > 0) {

    echo $consoleColor->apply("color_44", 'Terdapat ' . $count_no_taspen . ' data npwp...') . PHP_EOL . PHP_EOL;

    $reader_cek = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $spreadsheet_cek = $reader_cek->load('template/spt_tahunan_taspen.xlsx');
    $sheet_cek = $spreadsheet_cek->getSheetByName('data');

    $app    = new getNoTaspen();

    for ($i = 0; $i < $count_no_taspen; $i++) {
        echo $consoleColor->apply("color_208", 'Tarik Data SPT Tahunan taspen NPWP ' . $list_no_taspen[$i][0] . ' Tahun ' . $tahun . ' Urutan ke ' . ($i + 1) . ' dari ' . $count_no_taspen . '...') . PHP_EOL . PHP_EOL;

        $do_no_taspen = $app->doNoTaspen($list_no_taspen[$i][0], $tahun);

        # Pecah Data untuk mencari No Taspen dan Link Detail Bupotnya 
        $data_awal = get_string_between($do_no_taspen, '<tbody>', '</tbody>');
        $replace = str_replace('<td>', '', $data_awal);
        $replace1 = str_replace('</tr>', '', $replace);
        $replace2 = str_replace(array("\r", "\n"), '', $replace1);

        $pecah = explode('<tr>', $replace2);
        $pecah_data = array_map(function ($val) {
            return explode('</td>', $val);
        }, $pecah);

        $jml_data = count((array)$pecah_data);
        $jml_data_tampil = $jml_data - 1;

        echo $consoleColor->apply("color_157", '           Terdapat ' . $jml_data_tampil . ' data No Taspen') . PHP_EOL . PHP_EOL;

        # Proses Penarikan Data Detail nya, tergantung berapa data No Taspen
        for ($a = 1; $a < $jml_data; $a++) {

            $array_taspen = explode(" :", str_replace(['<b>', '</b>', '<br>', '</br>', '<br/>'], '', $pecah_data[$a][0]));
            $array_taspen = str_replace(array("\r", "\n"), '', $array_taspen);

            #Mengambil data No Taspen
            @$no_taspen = trim(preg_replace('/\s\s+/', ' ', str_replace([' ', 'NIP'], '', $array_taspen[1])));

            #Mengambil data Link untuk detail datanya
            @$pecah_link = str_replace('<a target="_blank" href ="', '', $pecah_data[$a][3]);
            $pecah_link1 = str_replace('"><button type="button" class="btn btn-warning"><i class="flaticon2-fax"></i> Cetak</button></a>', '', $pecah_link);
            $pecah_link2 = trim(preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $pecah_link1)));

            #Link eksekusi data detailnya
            $link =  'https://services.taspen.co.id/e-spt/' . $pecah_link2 . '';

            $do_spt_taspen = $app->do_spt_taspen($link);

            #Proses pecah data Bupot 1721-A2 dari web Taspen
            preg_match_all('#<tr[^>]*>(.*?)</tr>#is', $do_spt_taspen, $lines_ex);

            $result_ex = array();

            foreach ($lines_ex[1] as $k_ex => $line_ex) {
                preg_match_all('#<td[^>]*>(.*?)</td>#is', $line_ex, $cell_ex);

                foreach ($cell_ex[1] as $cell_ex) {
                    $result_ex[$k_ex][] = trim($cell_ex);
                }
            }

            @$npwp_bendahara             =   $result_ex[4][3];
            @$nama_bendahara             =   $result_ex[3][2];
            @$npwp_wp                    =   $result_ex[5][2];
            @$nama_wp                    =   $result_ex[7][2];
            @$alamat_wp                  =   $result_ex[9][2];
            @$jabatan_wp                 =   $result_ex[9][5];
            @$no_bupot                   =   str_replace(['&nbsp;', '&nbsp;', 'NOMOR :'], '', $result_ex[2][0]);
            @$tgl_bupot                  =   str_replace(['<table width="100%" border="0" class="allBorder">', '<tr>', '<td align="center">', '<br>'], '', $result_ex[41][4]);
            @$tgl_bupot                  =   str_replace(array("\r", "\n"), '', $tgl_bupot);
            @$tgl_bupot1                 =   trim(preg_replace('/\s\s+/', ' ', $tgl_bupot));
            @$tgl_bupot2                 =   preg_replace('~(<img.*>)~', '', $tgl_bupot1);
            @$ph_bruto                   =   str_replace(',', '', $result_ex[23][2]);
            @$pengurangan                =   str_replace(',', '', $result_ex[27][2]);
            @$ph_netto                   =   str_replace(',', '', $result_ex[29][2]);
            @$ptkp                       =   str_replace(',', '', $result_ex[32][2]);
            @$pkp                        =   str_replace(',', '', $result_ex[33][2]);
            @$pph_terutang_setahun       =   str_replace(',', '', $result_ex[34][2]);
            @$pph_terutang_sebelumnya    =   str_replace(',', '', $result_ex[35][2]);
            @$pph_terutang               =   str_replace(',', '', $result_ex[36][2]);
            @$pph_dipotong               =   str_replace(',', '', $result_ex[37][2]);

            #Proses mindah data array ke excel
            $sheet_sug = $spreadsheet_cek->getSheetByName('data');
            $sheet_sug->getTabColor()->setRGB('FFD966');

            $max_row_sug = $sheet_sug->getHighestRow();

            $s = $max_row_sug + 1;

            $sheet_sug->setCellValue('A' . $s, $list_no_taspen[$i][0]);
            $sheet_sug->setCellValue('B' . $s, $no_taspen);
            $sheet_sug->setCellValue('C' . $s, $npwp_bendahara);
            $sheet_sug->setCellValue('D' . $s, $nama_bendahara);
            $sheet_sug->setCellValue('E' . $s, $npwp_wp);
            $sheet_sug->setCellValue('F' . $s, $nama_wp);
            $sheet_sug->setCellValue('G' . $s, $alamat_wp);
            $sheet_sug->setCellValue('H' . $s, $jabatan_wp);
            $sheet_sug->setCellValue('I' . $s, $no_bupot);
            $sheet_sug->setCellValue('J' . $s, $tgl_bupot2);
            $sheet_sug->setCellValue('K' . $s, $ph_bruto);
            $sheet_sug->setCellValue('L' . $s, $pengurangan);
            $sheet_sug->setCellValue('M' . $s, $ph_netto);
            $sheet_sug->setCellValue('N' . $s, $ptkp);
            $sheet_sug->setCellValue('O' . $s, $pkp);
            $sheet_sug->setCellValue('P' . $s, $pph_terutang_setahun);
            $sheet_sug->setCellValue('Q' . $s, $pph_terutang_sebelumnya);
            $sheet_sug->setCellValue('R' . $s, $pph_terutang);
            $sheet_sug->setCellValue('S' . $s, $pph_dipotong);

            $s++;

            sleep(rand(0.5, 1));
        }
    }

    $writer_cek = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet_cek, 'Xlsx');
    date_default_timezone_set('Asia/jakarta');
    $writer_cek->save('hasil/spt_tahunan_taspen_' . date('dmY_His') . '.xlsx');

    echo 'File hasil download SPT Tahunan Taspen dapat dibuka di folder hasil...' . PHP_EOL . PHP_EOL . 'Terima kasih...' . PHP_EOL . PHP_EOL;
} else {
    echo 'Tidak ditemukan data No Taspen!' . PHP_EOL . 'Periksa file npwp_taspen.xlsx di folder input!' . PHP_EOL . PHP_EOL;
}
