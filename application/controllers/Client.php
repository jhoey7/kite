<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends CI_Controller {
	public function __construct() {
        parent::__construct();

		$configisg = $this->configisg();

		global $host;
		global $user;
		global $password;
		global $token;

		$host = $configisg['host'];
		$user = $configisg['user'];
		$password = $configisg['password'];
		$token = $configisg['token'];
    }
	
	private function configisg() {
		$apppath =  str_replace("\\", "/", APPPATH);
		$fileconfig = str_replace("application/", "", $apppath.'client.txt');
		$file = file_get_contents($fileconfig, true);

		$lines = explode("\n", $file);

		$configisg['host'] = preg_replace('/^[^=]*=/', '', $lines[0]);
		$configisg['user'] = preg_replace('/^[^=]*=/', '', $lines[1]);
		$configisg['password'] = preg_replace('/^[^=]*=/', '', $lines[2]);
		$configisg['token'] = preg_replace('/^[^=]*=/', '', $lines[3]);
		return $configisg;
	}

	public function login() {
		global $host, $user, $password, $token;

		$ch     = curl_init();
		$url = $host."/user/login";
		$email = $user;
		$password = $password;

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
			$apppath =  str_replace("\\", "/", APPPATH);
			$file = str_replace("application/", "", $apppath.'client.txt');
			
			$lines = file($file);
			$lines[3] = '_token=' . $arrData['data']['token'];
			file_put_contents($file, implode('', $lines));
		}
	}

	public function execute() {
		global $host, $user, $password, $token;

		$this->load->library('PHPExcel');

		$fileList = glob('/Applications/XAMPP/xamppfiles/htdocs/kite/*.xls');

		foreach ($fileList as $filename) {
			if (is_file($filename)) {
				$file = basename($filename, ".xls");
				$arrfile = explode("_", $file);

				PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
				$objPHPExcel = PHPExcel_IOFactory::load($file.".xls");
				$objPHPExcel->setActiveSheetIndex(0);

				foreach($objPHPExcel->getWorksheetIterator() as $worksheet){
					$highestRow         = $worksheet->getHighestRow(); 

					$highestColumn      = $worksheet->getHighestColumn(); 
					$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
					$arrHeader = array();



					// if (strtolower($arrfile[0]) == "laporanpemasukanbahanbaku") {
					// 	for ($row=2; $row <= $highestRow; $row++) {
					// 		for ($col=0; $col <= ($highestColumnIndex-1); $col++) {

					// 			$key = str_replace("'","",$worksheet->getCellByColumnAndRow(5, $row)->getCalculatedValue());

					// 			$kode_dokumen = str_replace(array("BC ","."), array("",""),$worksheet->getCellByColumnAndRow(1, $row)->getCalculatedValue());
					// 			$kode_valuta = str_replace("'", "",$worksheet->getCellByColumnAndRow(11, $row)->getCalculatedValue());
					// 			$no_daftar = str_replace("'", "",$worksheet->getCellByColumnAndRow(2, $row)->getCalculatedValue());
					// 			$kurs = 0;

					// 			$seri_barang = str_replace("'", "",$worksheet->getCellByColumnAndRow(4, $row)->getCalculatedValue());
					// 			$kd_brg = str_replace("'", "",$worksheet->getCellByColumnAndRow(7, $row)->getCalculatedValue());
					// 			$kd_satuan = str_replace("'", "",$worksheet->getCellByColumnAndRow(9, $row)->getCalculatedValue());
					// 			$jml_satuan = str_replace("'", "",$worksheet->getCellByColumnAndRow(10, $row)->getCalculatedValue());
					// 			$harga = str_replace("'", "",$worksheet->getCellByColumnAndRow(12, $row)->getCalculatedValue());
					// 			$kd_gudang = str_replace("'", "",$worksheet->getCellByColumnAndRow(13, $row)->getCalculatedValue());
					// 			$no_bl = str_replace("'", "",$worksheet->getCellByColumnAndRow(16, $row)->getCalculatedValue());
					// 			$kondisi_brg = "B";
					// 			$kd_negara = str_replace("'", "",$worksheet->getCellByColumnAndRow(15, $row)->getCalculatedValue());
					// 			$uraian_barang = str_replace("'", "",$worksheet->getCellByColumnAndRow(8, $row)->getCalculatedValue());

					// 			$tmp = $worksheet->getCellByColumnAndRow(3,$row)->getCalculatedValue();
					// 			$tgl_daftar = "20".substr($tmp, 4, 2)."-".substr($tmp, 2, 2)."-".substr($tmp, 0, 2);

					// 			$tgl_dok_internal = PHPExcel_Style_NumberFormat::toFormattedString($worksheet->getCellByColumnAndRow(6,$row)->getCalculatedValue(),PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
					// 			if(strpos($tgl_dok_internal,"/")===false){
					// 				$tgl_dok_internal = date('Y-m-d',strtotime($tgl_dok_internal));
					// 			}else{
					// 				$tgl_dok_internal = $this->dateformat($tgl_dok_internal);		
					// 			}

					// 			if ($kode_dokumen == "LOKAL") {
					// 				$asal_barang = "L";
					// 			} else {
					// 				$asal_barang = "I";
					// 			}
					// 		}
					// 		if (!in_array($key, $arrHeader[$key])) {
					// 			$arrHeader[$key] = array(
     //                                "tipe_file" => "IN",
     //                                "kode_dokumen"=> $kode_dokumen,
     //                                "nomor_aju"=> "-",
     //                                "nomor_daftar"=> $no_daftar,
     //                                "tanggal_daftar"=> $tgl_daftar,
     //                                "nomor_dok_internal"=> $key,
     //                                "tanggal_dok_internal"=> $tgl_dok_internal,
     //                                "id_vendor"=> "-",
     //                                "nama_vendor"=> "-",
     //                                "kode_valuta"=> $kode_valuta,
     //                                "kurs"=> $kurs,
     //                                "file_name"=> $file.".xls",
     //                                "details"=> [array(
     //                                        "seri_barang"=> $seri_barang,
     //                                        "kode_barang"=> $kd_brg,
     //                                        "kode_satuan"=> $kd_satuan,
     //                                        "jumlah_satuan"=> $jml_satuan,
     //                                        "harga_barang"=> $harga,
     //                                        "kode_gudang"=> $kd_gudang,
     //                                        "nomor_bl"=> $no_bl,
     //                                        "kode_hs"=> "",
     //                                        "kondisi_barang"=> $kondisi_brg,
     //                                        "kode_negara"=> $kd_negara,
     //                                        "asal_barang"=> $asal_barang,
     //                                        "uraian_barang"=> $uraian_barang
     //                                )]
     //                            );
					// 		} else {
					// 			array_push($arrHeader[$key]['details'], array(
					// 				"seri_barang"=> $seri_barang,
     //                                "kode_barang"=> $kd_brg,
     //                                "kode_satuan"=> $kd_satuan,
     //                                "jumlah_satuan"=> $jml_satuan,
     //                                "harga_barang"=> $harga,
     //                                "kode_gudang"=> $kd_gudang,
     //                                "nomor_bl"=> $no_bl,
     //                                "kode_hs"=> "",
     //                                "kondisi_barang"=> $kondisi_brg,
     //                                "kode_negara"=> $kd_negara,
     //                                "asal_barang"=> $asal_barang,
     //                                "uraian_barang"=> $uraian_barang
					// 			));
					// 		}
					// 	}
					// } else
					if (strtolower($arrfile[0]) == "laporanpengeluaranhasilproduksi") {
						for ($row=2; $row <= $highestRow; $row++) {
							for ($col=0; $col <= ($highestColumnIndex-1); $col++) {

								$key = str_replace("'","",$worksheet->getCellByColumnAndRow(3, $row)->getCalculatedValue());

								$kode_dokumen = "30";
								$kode_valuta = str_replace("'", "",$worksheet->getCellByColumnAndRow(12, $row)->getCalculatedValue());
								$no_daftar = str_replace("'", "",$worksheet->getCellByColumnAndRow(1, $row)->getCalculatedValue());
								$kurs = 0;
								$id_customer = str_replace("'", "",$worksheet->getCellByColumnAndRow(5, $row)->getCalculatedValue());
								$nama_customer = str_replace("'", "",$worksheet->getCellByColumnAndRow(6, $row)->getCalculatedValue());

								$kd_brg = str_replace("'", "",$worksheet->getCellByColumnAndRow(8, $row)->getCalculatedValue());
								$kd_satuan = str_replace("'", "",$worksheet->getCellByColumnAndRow(10, $row)->getCalculatedValue());
								$jml_satuan = str_replace("'", "",$worksheet->getCellByColumnAndRow(11, $row)->getCalculatedValue());
								$harga = str_replace("'", "",$worksheet->getCellByColumnAndRow(13, $row)->getCalculatedValue());
								$kd_gudang = str_replace("'", "",$worksheet->getCellByColumnAndRow(13, $row)->getCalculatedValue());

								$kondisi_brg = "B";
								$kd_negara = str_replace("'", "",$worksheet->getCellByColumnAndRow(7, $row)->getCalculatedValue());
								$uraian_barang = str_replace("'", "",$worksheet->getCellByColumnAndRow(9, $row)->getCalculatedValue());

								$tgl_daftar = PHPExcel_Style_NumberFormat::toFormattedString($worksheet->getCellByColumnAndRow(2,$row)->getCalculatedValue(),PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
								if(strpos($tgl_daftar,"/")===false){
									$tgl_daftar = date('Y-m-d',strtotime($tgl_daftar));
								}else{
									$tgl_daftar = $this->dateformat($tgl_daftar);		
								}

								$tgl_dok_internal = PHPExcel_Style_NumberFormat::toFormattedString($worksheet->getCellByColumnAndRow(4,$row)->getCalculatedValue(),PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
								if(strpos($tgl_dok_internal,"/")===false){
									$tgl_dok_internal = date('Y-m-d',strtotime($tgl_dok_internal));
								}else{
									$tgl_dok_internal = $this->dateformat($tgl_dok_internal);		
								}
							}
							if (!in_array($key, $arrHeader[$key])) {
								$seri_barang = 1;
								$arrHeader[$key] = array(
                                    "tipe_file" => "OUT",
                                    "kode_dokumen"=> $kode_dokumen,
                                    "nomor_aju"=> "-",
                                    "nomor_daftar"=> $no_daftar,
                                    "tanggal_daftar"=> $tgl_daftar,
                                    "nomor_dok_internal"=> $key,
                                    "tanggal_dok_internal"=> $tgl_dok_internal,
                                    "id_customer"=> $id_customer,
                                    "nama_customer"=> $nama_customer,
                                    "kode_valuta"=> $kode_valuta,
                                    "kurs"=> $kurs,
                                    "file_name"=> $file.".xls",
                                    "details"=> [array(
                                            "seri_barang"=> $seri_barang,
                                            "kode_barang"=> $kd_brg,
                                            "kode_satuan"=> $kd_satuan,
                                            "jumlah_satuan"=> $jml_satuan,
                                            "harga_barang"=> $harga,
                                            "kode_gudang"=> $kd_gudang,
                                            "kode_hs"=> "",
                                            "kondisi_barang"=> $kondisi_brg,
                                            "kode_negara"=> $kd_negara,
                                            "uraian_barang"=> $uraian_barang
                                    )]
                                );
							} else {
								$seri_barang++;
								array_push($arrHeader[$key]['details'], array(
									"seri_barang"=> $seri_barang,
                                    "kode_barang"=> $kd_brg,
                                    "kode_satuan"=> $kd_satuan,
                                    "jumlah_satuan"=> $jml_satuan,
                                    "harga_barang"=> $harga,
                                    "kode_gudang"=> $kd_gudang,
                                    "kode_hs"=> "",
                                    "kondisi_barang"=> $kondisi_brg,
                                    "kode_negara"=> $kd_negara,
                                    "uraian_barang"=> $uraian_barang
								));
							}
						}
					}

					if (count($arrHeader) > 0) {
						foreach ($arrHeader as $val) {
							$ch     = curl_init();
							$url = $host."/pabean/realisasi_with_full_element";
							$email = $user;
							$password = $password;

							$json = json_encode(array("email"=>$email, "password"=>$password));
							$headerPost = array('Content-Type: application/json','authorization: '.$token);
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_POST, 1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
							curl_setopt($ch, CURLOPT_VERBOSE, true);
							curl_setopt($ch, CURLOPT_HTTPHEADER, $headerPost);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($val));

							$server_output = curl_exec($ch);
							$arrData = json_decode($server_output,TRUE); 
							curl_close($ch);
							if ($arrData['message'] == "Token Time Expire.") {
								$this->login();
							} else {
								echo json_encode($arrData['message']);
							}
						}
					}
					unlink($filename);
				}
			}
		}
	}

	function dateformat($date){
		if($date!="" && $date!="0000-00-00"){
			if (strstr($date, "-"))   {
			   $date = preg_split("/[\/]|[-]+/", $date);
			   $date = $date[2]."-".$date[1]."-".$date[0];
			   return $date;
			}
			else if (strstr($date, "/"))   {
			   $date = preg_split("/[\/]|[-]+/", $date);
			   $date = $date[2]."-".$date[1]."-".$date[0];
			   return $date;
			}
			else if (strstr($date, ".")) {
			   $date = preg_split("[.]", $date);
			   $date = $date[2]."-".$date[1]."-".$date[0];
			   return $date;
			}
			else {
				$tmp = $date;
				$date = "20".substr($tmp, 4, 2)."-".substr($tmp, 2, 2)."-".substr($tmp, 0, 2);
				return $date;
			}
		}
		return false;
	}

}