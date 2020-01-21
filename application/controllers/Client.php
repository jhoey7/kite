<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends CI_Controller {
	public $token;

	public function login() {
		$ch     = curl_init();
		$url = 'http://localhost:8080/kite/index.php/api/user/login';
		$email = "admin.essar@gmail.com";
		$password = "123abc";

		$json = json_encode(array("email"=>$email, "password"=>$password));
		$headerPost = array('Content-Type: application/json');
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headerPost);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$server_output = curl_exec($ch);
		$arrData = json_decode($server_output,TRUE);
		curl_close($ch);
		if (count($arrData) > 0) {
			SET_TOKEN;
			define('TOKEN',$arrData['data']['token']);
			echo $arrData['data']['token'];
		}
	}

	public function execute() {
		echo TOKEN;die();
		// $this->load->library('PHPExcel');
		// $error = "";

		// $fileList = glob('/home/HITACHI/ftpfolder/*.xls');
		// foreach ($fileList as $filename) {
		// 	if (is_file($filename)) {
		// 		$file = basename($filename, ".xls");
		// 		$arrfile = explode("_", $file);

		// 		if (strtolower($arrfile[0]) == "laporanpemasukanbahanbaku") {
		// 			PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
		// 			$objPHPExcel = PHPExcel_IOFactory::load($file);
		// 			$objPHPExcel->setActiveSheetIndex(0);

		// 			foreach($objPHPExcel->getWorksheetIterator() as $worksheet){
		// 				$highestRow         = $worksheet->getHighestRow(); 

		// 				$highestColumn      = $worksheet->getHighestColumn(); 
		// 				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		// 				$index = 0;
		// 				for ($row=2; $row <= $highestRow; $row++) {
		// 					for ($col=0; $col <= ($highestColumnIndex-1); $col++) {
		// 						$cell = $worksheet->getCellByColumnAndRow($col, $row);
		// 						$CELLDATA = $cell->getCalculatedValue();
		// 						$DATAEXCEL[$index][$field[$col]] = str_replace("'","",$CELLDATA);
		// 					}
		// 					$index++;
		// 				}

		// 				foreach ($DATAEXCEL as $VAL) {
		// 					if (count($arr_barang_validasi[$VAL['KODE_BARANG']]) == 0) {
		// 						$barang['kd_brg'] = $VAL['KODE_BARANG'];
		// 						$barang['jns_brg'] = $VAL['JENIS_BARANG'];
		// 						$barang['nm_brg'] = $VAL['URAIAN_BARANG'];
		// 						$barang['nilai_konversi'] = $VAL['NILAI_KONVERSI'];
		// 						$barang['kd_satuan'] = $VAL['KODE_SATUAN'];
		// 						$barang['kd_satuan_terkecil'] = $VAL['KODE_SATUAN_TERKECIL'];
		// 						$barang['kode_trader'] = $KODE_TRADER;
								
		// 						#insert ke master barang
		// 						$this->db->insert('tm_barang', $barang);
		// 						$id_barang = $this->db->insert_id();

		// 						#insert ke master barang gudnag
		// 						$this->db->insert('tm_barang_gudang', array(
		// 							"id_barang"=> $id_barang,
		// 							"id_gudang"=> $arr_gudang_validasi[$VAL['KODE_GUDANG']]['id']
		// 						));
		// 						$id_gudang = $this->db->insert_id();

		// 						$arr_barang_validasi[$VAL['KODE_BARANG']]['id'] = $id_barang;
		// 						$arr_barang_validasi[$VAL['KODE_BARANG']]['kd_brg'] = $VAL['KODE_BARANG'];
		// 						$arr_barang_validasi[$VAL['KODE_BARANG']]['jns_brg'] = $VAL['JENIS_BARANG'];
		// 						$berhasil += 1;
		// 					} else {
		// 						$id_barang = $arr_barang_validasi[$VAL['KODE_BARANG']]['id'];

		// 						$sql = "SELECT a.id FROM tm_barang_gudang a 
		// 								LEFT JOIN tm_barang b ON b.id = a.id_barang 
		// 								LEFT JOIN tm_warehouse c on c.id = a.id_gudang
		// 								WHERE a.id_barang = ".$this->db->escape($id_barang)." 
		// 								AND c.kode = ".$this->db->escape($VAL['KODE_GUDANG']);
		// 						$hasil = $this->db->query($sql);
		// 						if ($hasil->num_rows() == 0) {
		// 							$this->db->insert('tm_barang_gudang', array(
		// 								"id_barang"=> $id_barang,
		// 								"id_gudang"=> $arr_gudang_validasi[$VAL['KODE_GUDANG']]['id']
		// 							));
		// 							$berhasil += 1;
		// 						}
		// 					}
		// 				}
		// 			}
		// 		}
		// 	}
		// }
	}

}