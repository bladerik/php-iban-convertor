<?php
/**IBAN Convertor
 * Skonvertuje slovenske cisla uctov do IBAN tvaru
 * naprogramovane podla http://www.nbs.sk/sk/platobne-systemy/iban/vypocet-a-kontrola-iban-pre-slovensku-republiku
 *
 * @author Lukas Gregor
 */
require_once("php-iban/php-iban.php");
require_once("IbanBankCode.php");
class IbanConvertor {
	private $country = "SK";
	private $spaceAfter = 4;

	/**
	 * Skonvertuje slovenske cislo uctu do IBAN formatu a overi jeho spravnost.
	 * @return string iban - validny iban
	 */
	public function convert($bankCode, $accountNumber, $accountPrefix = "000000") 
	{
		$this->checkBankCode($bankCode);
		$bban = $this->getBBan($bankCode, $accountNumber, $accountPrefix);
		$suffix = $this->calculateCountryCode() . "00";
		return $this->buildIban($this->calculateControlNumber($bban, $suffix), $bban);
	}

	public function convertHumanReadable($bankCode, $accountNumber, $accountPrefix = "000000") 
	{
		$split = str_split($this->convert($bankCode, $accountNumber, $accountPrefix), $this->spaceAfter);
		return implode(" ", $split);
	}

	private function buildIban($controlNumber, $bban) 
	{
		$iban = $this->country . $controlNumber . $bban;
		if (!verify_iban($iban)) {
			throw new IbanConvertorException("Iban is in invalid format");
		}
		return $iban;
	}

	private function checkBankCode($bankCode) 
	{
		$reflection = new ReflectionClass('IbanBankCode');
		foreach ($reflection->getConstants() AS $constant) {
			if ($bankCode === $constant) return;
		}
		throw new IbanConvertorException("Unsupported bank code: " . $bankCode);
	}

	private function calculateCountryCode()
	{
		$parts = str_split($this->country, 1);
		$output = "";
		foreach ($parts AS $part) {
			$output .= $this->getLetterNumberValue($part);
		}
		return $output;
	}

	private function getBBan($bankCode, $accountNumber, $accountPrefix) 
	{
		return $bankCode . $accountPrefix . $accountNumber;
	}

	private function calculateControlNumber($bban, $suffix) 
	{
		//5) Z tohto čísla sa vypočíta modulo 97 (zvyšok po delení 97)
		//info: PHP si nepodari s obrovskym cislom ako 2000000002616082058282000 preto je potrebne pouzit bcmod
		//zdroj http://stackoverflow.com/questions/21077360/calculate-iban-account-number
		$mod = bcmod($bban . $suffix, 97);

		//6) Kontrolné číslo sa vypočíta odčítaním zvyšku po delení 97 vypočítaného v kroku 5 (t.j. '+cs+') od čísla 98
		return 98 - $mod;
	}

	private function getLetterNumberValue($letter) 
	{
		$letter = strtoupper($letter);
		$allowedLetters = range("A", "Z");
		if (!in_array($letter, $allowedLetters)) {
			throw new IbanConvertorException($letter . " is invalid and can't be converted to number value!");
		}
		$values = range(10, 35);
		return $values[array_search($letter, $allowedLetters)];
	}

} 

class IbanConvertorException extends Exception {}