<?php
/**
 * PDFBarcode.php
 *
 * @author BAUDRY Charly 
 * @copyright csFPDF
 */

namespace CS\FpdfBundle;

use CS\FpdfBundle\Exception\FPDFException;

class PDFBarcode
{

    private PDF $master;

	
	//** ************************************ **
	//** ******** PARAMETRES EAN 128 ******** **
	//** ************************************ **
	private array $EAN128_T128 = [
		[2, 1, 2, 2, 2, 2], //0 : [ ]
		[2, 2, 2, 1, 2, 2], //1 : [!]
		[2, 2, 2, 2, 2, 1], //2 : ["]
		[1, 2, 1, 2, 2, 3], //3 : [#]
		[1, 2, 1, 3, 2, 2], //4 : [$]
		[1, 3, 1, 2, 2, 2], //5 : [%]
		[1, 2, 2, 2, 1, 3], //6 : [&]
		[1, 2, 2, 3, 1, 2], //7 : [']
		[1, 3, 2, 2, 1, 2], //8 : [(]
		[2, 2, 1, 2, 1, 3], //9 : [)]
		[2, 2, 1, 3, 1, 2], //10 : [*]
		[2, 3, 1, 2, 1, 2], //11 : [+]
		[1, 1, 2, 2, 3, 2], //12 : [,]
		[1, 2, 2, 1, 3, 2], //13 : [-]
		[1, 2, 2, 2, 3, 1], //14 : [.]
		[1, 1, 3, 2, 2, 2], //15 : [/]
		[1, 2, 3, 1, 2, 2], //16 : [0]
		[1, 2, 3, 2, 2, 1], //17 : [1]
		[2, 2, 3, 2, 1, 1], //18 : [2]
		[2, 2, 1, 1, 3, 2], //19 : [3]
		[2, 2, 1, 2, 3, 1], //20 : [4]
		[2, 1, 3, 2, 1, 2], //21 : [5]
		[2, 2, 3, 1, 1, 2], //22 : [6]
		[3, 1, 2, 1, 3, 1], //23 : [7]
		[3, 1, 1, 2, 2, 2], //24 : [8]
		[3, 2, 1, 1, 2, 2], //25 : [9]
		[3, 2, 1, 2, 2, 1], //26 : [:]
		[3, 1, 2, 2, 1, 2], //27 : [;]
		[3, 2, 2, 1, 1, 2], //28 : [<]
		[3, 2, 2, 2, 1, 1], //29 : [=]
		[2, 1, 2, 1, 2, 3], //30 : [>]
		[2, 1, 2, 3, 2, 1], //31 : [?]
		[2, 3, 2, 1, 2, 1], //32 : [@]
		[1, 1, 1, 3, 2, 3], //33 : [A]
		[1, 3, 1, 1, 2, 3], //34 : [B]
		[1, 3, 1, 3, 2, 1], //35 : [C]
		[1, 1, 2, 3, 1, 3], //36 : [D]
		[1, 3, 2, 1, 1, 3], //37 : [E]
		[1, 3, 2, 3, 1, 1], //38 : [F]
		[2, 1, 1, 3, 1, 3], //39 : [G]
		[2, 3, 1, 1, 1, 3], //40 : [H]
		[2, 3, 1, 3, 1, 1], //41 : [I]
		[1, 1, 2, 1, 3, 3], //42 : [J]
		[1, 1, 2, 3, 3, 1], //43 : [K]
		[1, 3, 2, 1, 3, 1], //44 : [L]
		[1, 1, 3, 1, 2, 3], //45 : [M]
		[1, 1, 3, 3, 2, 1], //46 : [N]
		[1, 3, 3, 1, 2, 1], //47 : [O]
		[3, 1, 3, 1, 2, 1], //48 : [P]
		[2, 1, 1, 3, 3, 1], //49 : [Q]
		[2, 3, 1, 1, 3, 1], //50 : [R]
		[2, 1, 3, 1, 1, 3], //51 : [S]
		[2, 1, 3, 3, 1, 1], //52 : [T]
		[2, 1, 3, 1, 3, 1], //53 : [U]
		[3, 1, 1, 1, 2, 3], //54 : [V]
		[3, 1, 1, 3, 2, 1], //55 : [W]
		[3, 3, 1, 1, 2, 1], //56 : [X]
		[3, 1, 2, 1, 1, 3], //57 : [Y]
		[3, 1, 2, 3, 1, 1], //58 : [Z]
		[3, 3, 2, 1, 1, 1], //59 : [[]
		[3, 1, 4, 1, 1, 1], //60 : [\]
		[2, 2, 1, 4, 1, 1], //61 : []]
		[4, 3, 1, 1, 1, 1], //62 : [^]
		[1, 1, 1, 2, 2, 4], //63 : [_]
		[1, 1, 1, 4, 2, 2], //64 : [`]
		[1, 2, 1, 1, 2, 4], //65 : [a]
		[1, 2, 1, 4, 2, 1], //66 : [b]
		[1, 4, 1, 1, 2, 2], //67 : [c]
		[1, 4, 1, 2, 2, 1], //68 : [d]
		[1, 1, 2, 2, 1, 4], //69 : [e]
		[1, 1, 2, 4, 1, 2], //70 : [f]
		[1, 2, 2, 1, 1, 4], //71 : [g]
		[1, 2, 2, 4, 1, 1], //72 : [h]
		[1, 4, 2, 1, 1, 2], //73 : [i]
		[1, 4, 2, 2, 1, 1], //74 : [j]
		[2, 4, 1, 2, 1, 1], //75 : [k]
		[2, 2, 1, 1, 1, 4], //76 : [l]
		[4, 1, 3, 1, 1, 1], //77 : [m]
		[2, 4, 1, 1, 1, 2], //78 : [n]
		[1, 3, 4, 1, 1, 1], //79 : [o]
		[1, 1, 1, 2, 4, 2], //80 : [p]
		[1, 2, 1, 1, 4, 2], //81 : [q]
		[1, 2, 1, 2, 4, 1], //82 : [r]
		[1, 1, 4, 2, 1, 2], //83 : [s]
		[1, 2, 4, 1, 1, 2], //84 : [t]
		[1, 2, 4, 2, 1, 1], //85 : [u]
		[4, 1, 1, 2, 1, 2], //86 : [v]
		[4, 2, 1, 1, 1, 2], //87 : [w]
		[4, 2, 1, 2, 1, 1], //88 : [x]
		[2, 1, 2, 1, 4, 1], //89 : [y]
		[2, 1, 4, 1, 2, 1], //90 : [z]
		[4, 1, 2, 1, 2, 1], //91 : [{]
		[1, 1, 1, 1, 4, 3], //92 : [|]
		[1, 1, 1, 3, 4, 1], //93 : [}]
		[1, 3, 1, 1, 4, 1], //94 : [~]
		[1, 1, 4, 1, 1, 3], //95 : [DEL]
		[1, 1, 4, 3, 1, 1], //96 : [FNC3]
		[4, 1, 1, 1, 1, 3], //97 : [FNC2]
		[4, 1, 1, 3, 1, 1], //98 : [SHIFT]
		[1, 1, 3, 1, 4, 1], //99 : [Cswap]
		[1, 1, 4, 1, 3, 1], //100 : [Bswap]
		[3, 1, 1, 1, 4, 1], //101 : [Aswap]
		[4, 1, 1, 1, 3, 1], //102 : [FNC1]
		[2, 1, 1, 4, 1, 2], //103 : [Astart]
		[2, 1, 1, 2, 1, 4], //104 : [Bstart]
		[2, 1, 1, 2, 3, 2], //105 : [Cstart]
		[2, 3, 3, 1, 1, 1], //106 : [STOP]
		[2, 1], //107 : [END BAR]
	];
	private string $EAN128_ABCset = "";	// jeu des caractères éligibles au C128
	private string $EAN128_Aset = "";	// Set A du jeu des caractères éligibles
	private string $EAN128_Bset = "";	// Set B du jeu des caractères éligibles
	private string $EAN128_Cset = "";	// Set C du jeu des caractères éligibles
	private $EAN128_SetFrom;	   // Convertisseur source des jeux vers le tableau
	private $EAN128_SetTo;	   // Convertisseur destination des jeux vers le tableau
	private array $EAN128_JStart = ["A" => 103, "B" => 104, "C" => 105]; // Caractères de sélection de jeu au début du C128
	private array $EAN128_JSwap = ["A" => 101, "B" => 100, "C" => 99]; // Caractères de changement de jeu
	
    /**
     * PDFBarcode constructor.
     *
     * @param PDF $oPDF Instance of the main PDF class
     */
    public function __construct(PDF $oPDF){
        $this->master = $oPDF;
		
		//Character set
		for($i = 32; $i <= 95; $i++){
			$this->EAN128_ABCset .= chr($i);
		}
		$this->EAN128_Aset = $this->EAN128_ABCset;
		$this->EAN128_Bset = $this->EAN128_ABCset;

		for($i = 0; $i <= 31; $i++){
			$this->EAN128_ABCset .= chr($i);
			$this->EAN128_Aset .= chr($i);
		}
		for($i = 96; $i <= 127; $i++){
			$this->EAN128_ABCset .= chr($i);
			$this->EAN128_Bset .= chr($i);
		}
		//Controle 128
		for($i = 200; $i <= 210; $i++){
			$this->EAN128_ABCset .= chr($i);
			$this->EAN128_Aset .= chr($i);
			$this->EAN128_Bset .= chr($i);
		}
		$this->EAN128_Cset = "0123456789" . chr(206);

		//Set A & B converter
		for($i = 0; $i < 96; $i++){
			@$this->EAN128_SetFrom["A"] .= chr($i);
			@$this->EAN128_SetFrom["B"] .= chr($i + 32);
			@$this->EAN128_SetTo["A"] .= chr(($i < 32) ? $i + 64 : $i - 32);
			@$this->EAN128_SetTo["B"] .= chr($i);
		}
		//Set A & B control
		for($i = 96; $i < 107; $i++){
			@$this->EAN128_SetFrom["A"] .= chr($i + 104);
			@$this->EAN128_SetFrom["B"] .= chr($i + 104);
			@$this->EAN128_SetTo["A"] .= chr($i);
			@$this->EAN128_SetTo["B"] .= chr($i);
		}
    }
	
	
	
	//** ************ **
	//** GENCOD EAN13 **
	//** ************ **
	public function EAN13($x, $y, $barcode, $w = .35, $h = 16){
		if($barcode === null){
			return;
		}
		$this->Barcode($x, $y, $barcode, $w, $h, 13);
	}

	public function UPC_A($x, $y, $barcode, $w = .35, $h = 16){
		if($barcode === null){
			return;
		}
		$this->Barcode($x, $y, $barcode, $w, $h, 12);
	}

	private function GetCheckDigit($barcode){
		//Compute the check digit
		$sum = 0;
		for($i = 1; $i <= 11; $i += 2){
			$sum += 3 * $barcode[$i];
		}
		for($i = 0; $i <= 10; $i += 2){
			$sum += $barcode[$i];
		}
		$r = $sum % 10;
		if($r > 0){
			$r = 10 - $r;
		}
		return $r;
	}

	private function TestCheckDigit($barcode){
		//Test validity of check digit
		$sum = 0;
		for($i = 1; $i <= 11; $i += 2){
			$sum += 3 * $barcode[$i];
		}
		for($i = 0; $i <= 10; $i += 2){
			$sum += $barcode[$i];
		}
		return ($sum + $barcode[12]) % 10 == 0;
	}

	public function Barcode($x, $y, $barcode, $w, $h, $len){
		//Padding
		$barcode = str_pad($barcode, $len - 1, '0', STR_PAD_LEFT);
		if($len == 12){
			$barcode = '0' . $barcode;
		}
		//Add or control the check digit
		if(strlen($barcode) == 12){
			$barcode .= $this->GetCheckDigit($barcode);
		}elseif(!$this->TestCheckDigit($barcode)){
			throw new FPDFException('Incorrect check digit', FPDFException::BARCODE_INCORECT_DIGIT_CHECK);
		}
		//Convert digits to bars
		$codes = array(
			'A' => array(
				'0' => '0001101', '1' => '0011001', '2' => '0010011', '3' => '0111101', '4' => '0100011',
				'5' => '0110001', '6' => '0101111', '7' => '0111011', '8' => '0110111', '9' => '0001011'),
			'B' => array(
				'0' => '0100111', '1' => '0110011', '2' => '0011011', '3' => '0100001', '4' => '0011101',
				'5' => '0111001', '6' => '0000101', '7' => '0010001', '8' => '0001001', '9' => '0010111'),
			'C' => array(
				'0' => '1110010', '1' => '1100110', '2' => '1101100', '3' => '1000010', '4' => '1011100',
				'5' => '1001110', '6' => '1010000', '7' => '1000100', '8' => '1001000', '9' => '1110100')
		);
		$parities = array(
			'0' => array('A', 'A', 'A', 'A', 'A', 'A'),
			'1' => array('A', 'A', 'B', 'A', 'B', 'B'),
			'2' => array('A', 'A', 'B', 'B', 'A', 'B'),
			'3' => array('A', 'A', 'B', 'B', 'B', 'A'),
			'4' => array('A', 'B', 'A', 'A', 'B', 'B'),
			'5' => array('A', 'B', 'B', 'A', 'A', 'B'),
			'6' => array('A', 'B', 'B', 'B', 'A', 'A'),
			'7' => array('A', 'B', 'A', 'B', 'A', 'B'),
			'8' => array('A', 'B', 'A', 'B', 'B', 'A'),
			'9' => array('A', 'B', 'B', 'A', 'B', 'A')
		);
		$ahGlob = 1;
		$addLen = [];
		$addLen[0] = $ahGlob;
		$addLen[2] = $ahGlob;
		$addLen[46] = $ahGlob;
		$addLen[48] = $ahGlob;
		$code = '101';
		$p = $parities[$barcode[0]];
		for($i = 1; $i <= 6; $i++){
			$code .= $codes[$p[$i - 1]][$barcode[$i]];
		}
		$code .= '01010';
		for($i = 7; $i <= 12; $i++){
			$code .= $codes['C'][$barcode[$i]];
		}
		$code .= '101';

		//Draw bars
		for($i = 0; $i < strlen($code); $i++){
			if($code[$i] == '1'){
				$ah = 0;
				if(array_key_exists($i, $addLen)){
					$ah = $addLen[$i];
				}
				$this->master->Rect($x + $i * $w, $y, $w, $h + $ah, 'F');
			}
		}
		//Styled text
		$_spc = 5 * $w;
		$_x1 = $x - ($_spc * 1.5);
		$_x2 = $x + $_spc;
		$_x3 = $x + ($_spc * 11);
		$this->master->SetXY($_x1, $y + $h);
		$this->master->Cell($_spc, $this->master->GetUserFontSize(), substr($barcode, 0, 1), 0, 1, "C");
		$this->master->SetXY($_x2, $y + $h);
		$this->master->Cell($_spc * 8, $this->master->GetUserFontSize(), substr($barcode, 1, 6), 0, 1, "C");
		$this->master->SetXY($_x3, $y + $h);
		$this->master->Cell($_spc * 8, $this->master->GetUserFontSize(), substr($barcode, 7, 6), 0, 1, "C");
	}

	//** ************* **/
	//** GENCOD EAN128 **/
	//** ************* **/
	function EAN128($x, $y, $code, $w, $h){
		$Aguid = "";
		$Bguid = "";
		$Cguid = "";
		for($i = 0; $i < strlen($code); $i++){
			$needle = substr($code, $i, 1);
			$Aguid .= ((strpos($this->EAN128_Aset, $needle) === false) ? "N" : "O");
			$Bguid .= ((strpos($this->EAN128_Bset, $needle) === false) ? "N" : "O");
			$Cguid .= ((strpos($this->EAN128_Cset, $needle) === false) ? "N" : "O");
		}

		$SminiC = "OOOO";
		$IminiC = 4;

		$crypt = "";
		while($code > ""){
			// BOUCLE PRINCIPALE DE CODAGE
			$i = strpos($Cguid, $SminiC); // forçage du jeu C, si possible
			if($i !== false){
				$Aguid [$i] = "N";
				$Bguid [$i] = "N";
			}

			if(substr($Cguid, 0, $IminiC) == $SminiC){								  // jeu C
				$crypt .= chr(($crypt > "") ? $this->EAN128_JSwap["C"] : $this->EAN128_JStart["C"]);  // début Cstart, sinon Cswap
				$made = strpos($Cguid, "N");											 // étendu du set C
				if($made === false){
					$made = strlen($Cguid);
				}
				if(fmod($made, 2) == 1){
					$made--;															// seulement un nombre pair
				}
				for($i = 0; $i < $made; $i += 2){
					$crypt .= chr(strval(substr($code, $i, 2)));						  // conversion 2 par 2
				}
				$jeu = "C";
			}else{
				$madeA = strpos($Aguid, "N");											// étendu du set A
				if($madeA === false){
					$madeA = strlen($Aguid);
				}
				$madeB = strpos($Bguid, "N");											// étendu du set B
				if($madeB === false){
					$madeB = strlen($Bguid);
				}
				$made = (($madeA < $madeB) ? $madeB : $madeA );						 // étendu traitée
				$jeu = (($madeA < $madeB) ? "B" : "A" );								// Jeu en cours

				$crypt .= chr(($crypt > "") ? $this->EAN128_JSwap[$jeu] : $this->EAN128_JStart[$jeu]); // début start, sinon swap

				$crypt .= strtr(substr($code, 0, $made), $this->EAN128_SetFrom[$jeu], $this->EAN128_SetTo[$jeu]); // conversion selon jeu
			}
			$code = substr($code, $made);										   // raccourcir légende et guides de la zone traitée
			$Aguid = substr($Aguid, $made);
			$Bguid = substr($Bguid, $made);
			$Cguid = substr($Cguid, $made);
		}																		  // FIN BOUCLE PRINCIPALE

		$check = ord($crypt[0]);												   // calcul de la somme de contrôle
		for($i = 0; $i < strlen($crypt); $i++){
			$check += (ord($crypt[$i]) * $i);
		}
		$check %= 103;

		$crypt .= chr($check) . chr(106) . chr(107);							   // Chaine cryptée complète
		$i = (strlen($crypt) * 11) - 8;											// calcul de la largeur du module
		$modul = $w / $i;

		for($i = 0; $i < strlen($crypt); $i++){									  // BOUCLE D'IMPRESSION
			$c = $this->EAN128_T128[ord($crypt[$i])];
			for($j = 0; $j < count($c); $j++){
				$this->master->Rect($x, $y, $c[$j] * $modul, $h, "F");
				$x += ($c[$j++] + $c[$j]) * $modul;
			}
		}
	}
	
	public function CODE39($x, $y, $code, $ext = true, $cks = false, $w = 0.4, $h = 20, $wide = true) {
		//suppression des accents
		$code = strtr($code, 'àâäéèêëìîïòôöùûü', 'aaaeeeeiiiooouuu');

		//This was made by the original creator, will have to check if this can be replaced or reveresed when function ends
		$this->SetFont('Arial', '', 10);
		$this->Text($x, $y+$h+4, $code);

		if($ext) {
			//encodage étendu
			$code = $this->CODE39_EncodeExt($code);
		}
		else {
			//passage en majuscules
			$code = strtoupper($code);
			//contrôle validité
			if(!preg_match('|^[0-9A-Z. $/+%-]*$|', $code)){
				throw new FPDFException('Invalid barcode value: '.$code, FPDFException::BARCODE_39_INVALID_VALUE);
			}
		}

		//calcul du checksum
		if ($cks){
			$code .= $this->CODE39_Checksum($code);
		}

		//ajout des caractères début / fin
		$code = '*'.$code.'*';

		//tableaux de correspondance caractères / barres
		$narrow_encoding = [
			'0' => '101001101101', '1' => '110100101011', '2' => '101100101011',
			'3' => '110110010101', '4' => '101001101011', '5' => '110100110101',
			'6' => '101100110101', '7' => '101001011011', '8' => '110100101101',
			'9' => '101100101101', 'A' => '110101001011', 'B' => '101101001011',
			'C' => '110110100101', 'D' => '101011001011', 'E' => '110101100101',
			'F' => '101101100101', 'G' => '101010011011', 'H' => '110101001101',
			'I' => '101101001101', 'J' => '101011001101', 'K' => '110101010011',
			'L' => '101101010011', 'M' => '110110101001', 'N' => '101011010011',
			'O' => '110101101001', 'P' => '101101101001', 'Q' => '101010110011',
			'R' => '110101011001', 'S' => '101101011001', 'T' => '101011011001',
			'U' => '110010101011', 'V' => '100110101011', 'W' => '110011010101',
			'X' => '100101101011', 'Y' => '110010110101', 'Z' => '100110110101',
			'-' => '100101011011', '.' => '110010101101', ' ' => '100110101101',
			'*' => '100101101101', '$' => '100100100101', '/' => '100100101001',
			'+' => '100101001001', '%' => '101001001001'
		];

		$wide_encoding = [
			'0' => '101000111011101', '1' => '111010001010111', '2' => '101110001010111',
			'3' => '111011100010101', '4' => '101000111010111', '5' => '111010001110101',
			'6' => '101110001110101', '7' => '101000101110111', '8' => '111010001011101',
			'9' => '101110001011101', 'A' => '111010100010111', 'B' => '101110100010111',
			'C' => '111011101000101', 'D' => '101011100010111', 'E' => '111010111000101',
			'F' => '101110111000101', 'G' => '101010001110111', 'H' => '111010100011101',
			'I' => '101110100011101', 'J' => '101011100011101', 'K' => '111010101000111',
			'L' => '101110101000111', 'M' => '111011101010001', 'N' => '101011101000111',
			'O' => '111010111010001', 'P' => '101110111010001', 'Q' => '101010111000111',
			'R' => '111010101110001', 'S' => '101110101110001', 'T' => '101011101110001',
			'U' => '111000101010111', 'V' => '100011101010111', 'W' => '111000111010101',
			'X' => '100010111010111', 'Y' => '111000101110101', 'Z' => '100011101110101',
			'-' => '100010101110111', '.' => '111000101011101', ' ' => '100011101011101',
			'*' => '100010111011101', '$' => '100010001000101', '/' => '100010001010001',
			'+' => '100010100010001', '%' => '101000100010001'
		];

		//le code barre est déterminé en version large ou étroite (meilleure lisibilité)
		//large observe un rapport 3:1 pour le rapport barre large / barre etroite
		//etroit                   2:1
		$encoding = $wide ? $wide_encoding : $narrow_encoding;

		//espace inter-caractère
		$gap = ($w > 0.29) ? '00' : '0';

		//encodage
		$encode = '';
		for ($i = 0; $i< strlen($code); $i++){
			$encode .= $encoding[$code[$i]].$gap;
		}

		//dessin
		$this->CODE39_Draw($encode, $x, $y, $w, $h);
	}

	private function CODE39_Checksum($code) {

		//somme des positions des caractères en démarrant de zéro
		//somme modulo 43
		//le caractère de contrôle est celui à la position du modulo
		//exemple : 115 % 43 = 29 -> 'T' est à la place 29 dans le tableau

		$chars = [
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',
			'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
			'W', 'X', 'Y', 'Z', '-', '.', ' ', '$', '/', '+', '%'
		];
		$sum = 0;
		for ($i=0 ; $i<strlen($code); $i++) {
			$a = array_keys($chars, $code[$i]);
			$sum += $a[0];
		}
		$r = $sum % 43;
		return $chars[$r];
	}

	private function CODE39_EncodeExt($code) {

		//encodage en code 39 étendu

		$encode = [
			chr(0) => '%U', chr(1) => '$A', chr(2) => '$B', chr(3) => '$C',
			chr(4) => '$D', chr(5) => '$E', chr(6) => '$F', chr(7) => '$G',
			chr(8) => '$H', chr(9) => '$I', chr(10) => '$J', chr(11) => '£K',
			chr(12) => '$L', chr(13) => '$M', chr(14) => '$N', chr(15) => '$O',
			chr(16) => '$P', chr(17) => '$Q', chr(18) => '$R', chr(19) => '$S',
			chr(20) => '$T', chr(21) => '$U', chr(22) => '$V', chr(23) => '$W',
			chr(24) => '$X', chr(25) => '$Y', chr(26) => '$Z', chr(27) => '%A',
			chr(28) => '%B', chr(29) => '%C', chr(30) => '%D', chr(31) => '%E',
			chr(32) => ' ', chr(33) => '/A', chr(34) => '/B', chr(35) => '/C',
			chr(36) => '/D', chr(37) => '/E', chr(38) => '/F', chr(39) => '/G',
			chr(40) => '/H', chr(41) => '/I', chr(42) => '/J', chr(43) => '/K',
			chr(44) => '/L', chr(45) => '-', chr(46) => '.', chr(47) => '/O',
			chr(48) => '0', chr(49) => '1', chr(50) => '2', chr(51) => '3',
			chr(52) => '4', chr(53) => '5', chr(54) => '6', chr(55) => '7',
			chr(56) => '8', chr(57) => '9', chr(58) => '/Z', chr(59) => '%F',
			chr(60) => '%G', chr(61) => '%H', chr(62) => '%I', chr(63) => '%J',
			chr(64) => '%V', chr(65) => 'A', chr(66) => 'B', chr(67) => 'C',
			chr(68) => 'D', chr(69) => 'E', chr(70) => 'F', chr(71) => 'G',
			chr(72) => 'H', chr(73) => 'I', chr(74) => 'J', chr(75) => 'K',
			chr(76) => 'L', chr(77) => 'M', chr(78) => 'N', chr(79) => 'O',
			chr(80) => 'P', chr(81) => 'Q', chr(82) => 'R', chr(83) => 'S',
			chr(84) => 'T', chr(85) => 'U', chr(86) => 'V', chr(87) => 'W',
			chr(88) => 'X', chr(89) => 'Y', chr(90) => 'Z', chr(91) => '%K',
			chr(92) => '%L', chr(93) => '%M', chr(94) => '%N', chr(95) => '%O',
			chr(96) => '%W', chr(97) => '+A', chr(98) => '+B', chr(99) => '+C',
			chr(100) => '+D', chr(101) => '+E', chr(102) => '+F', chr(103) => '+G',
			chr(104) => '+H', chr(105) => '+I', chr(106) => '+J', chr(107) => '+K',
			chr(108) => '+L', chr(109) => '+M', chr(110) => '+N', chr(111) => '+O',
			chr(112) => '+P', chr(113) => '+Q', chr(114) => '+R', chr(115) => '+S',
			chr(116) => '+T', chr(117) => '+U', chr(118) => '+V', chr(119) => '+W',
			chr(120) => '+X', chr(121) => '+Y', chr(122) => '+Z', chr(123) => '%P',
			chr(124) => '%Q', chr(125) => '%R', chr(126) => '%S', chr(127) => '%T'
		];

		$code_ext = '';
		for ($i = 0 ; $i<strlen($code); $i++) {
			if (ord($code[$i]) > 127){
				throw new FPDFException('Invalid character: '.$code[$i], FPDFException::BARCODE_39_INVALID_VALUE);
			}
			$code_ext .= $encode[$code[$i]];
		}
		return $code_ext;
	}

	private function CODE39_Draw($code, $x, $y, $w, $h) {

		//Dessine les barres

		for($i=0; $i<strlen($code); $i++) {
			if($code[$i] == '1'){
				$this->master->Rect($x+$i*$w, $y, $w, $h, 'F');
			}
		}
	}

}