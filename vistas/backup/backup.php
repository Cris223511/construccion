<?php
require("db.php");
class backupdb extends db
{
	private $ruta = "";
	function __construct()
	{
		parent::__construct();
		echo $this->config();
	}

	private function config(): string
	{
		date_default_timezone_set("America/Lima");
		$fecha = date("H-i-s_d-m-Y");
		// poner la ruta donde se guardará el archivo.
		// $this->ruta = "backup/backup/{$fecha}_{$this->getdb()}.sql";
		// $this->ruta = "C:/downloads/{$fecha}_{$this->getdb()}.sql";
		$ruta1 = "backup/backup/{$fecha}_{$this->getdb()}.sql";
		$ruta2 = "backup/backup/{$fecha}_{$this->getdb()}.sql";

		try {
			$this->ruta = $ruta1;

			$comando = "mysqldump -u {$this->getUsuario()} {$this->getdb()} > {$this->ruta} --all-tablespaces --column-statistics=0";
			echo '<script>window.location.href = window.location; window.alert("Se exportó la base de datos correctamente."); </script>';
			return system($comando);
		} catch (Exception $e) {
			// Si la ruta 1 no funciona, intenta con la ruta 2
			$this->ruta = $ruta2;

			if (is_writable("backup")) {
				if (file_exists($this->ruta)) {
					unlink($this->ruta);
				}

				$comando = "mysqldump -u {$this->getUsuario()} {$this->getdb()} > {$this->ruta} --all-tablespaces --column-statistics=0";
				echo '<script>window.location.href = window.location; window.alert("Se exportó la base de datos correctamente."); </script>';
				return system($comando);
			} else {
				return "El directorio no tiene permisos de escritura.";
			}
		}
	}


	public function getRuta(): string
	{
		return $this->ruta;
	}
}
