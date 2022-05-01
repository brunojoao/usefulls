<?php
namespace Csv\Generator;
use \Exception as Exception;

interface defaults{
  //Motor principal
  public static function run($args);
}
class CsvGenerator implements defaults
{
  public static function run($args,$msg=true)
  {
    //Imprime mensagem caso o segundo parametro da função seja TRUE, se for false roda no modo silencioso
    if($msg)$mensagem = "";
    try {
      //verificações caso tenha um header um body e um footer
      if (isset($args["header"]) && isset($args["body"]) && isset($args["footer"])) {
        //Caso se nome_arquivo e diretório não forem informados, atribui um valor vazio padrão
        if(!isset($args["nome_arquivo"])||$args["nome_arquivo"]=="")$args["nome_arquivo"]="semNome";
        if(!isset($args["diretorio"]))$args["diretorio"]="";

        header("Content-Type: text/csv;charset=utf-8");
        date_default_timezone_set('America/Sao_Paulo');
        //Gera um arquivo ordenavel devido ao formato da data YmdHis, este modelo permite ordenar corretamente pelo mais recente
        $fp = fopen($args["diretorio"].date('YmdHis')."__".$args["nome_arquivo"].".csv", 'w');
        if ($fp) {
          //Imprime header, linha unica
          fputcsv($fp, $args["header"], ";");
          //Imprime Body, várias linhas
          foreach ($args["body"] as $linha) {
            fputcsv($fp, $linha, ";");
          }
          //Imprime footer, linha unica
          fputcsv($fp, $args["footer"], ";");
          fclose($fp);
          if($msg)$mensagem = "Arquivo gerado!";
        } else {
          throw new Exception('Não foi possivel gerar o arquivo.');
        }
      } else {
        throw new Exception("Passe o parametro corretamente conforme o modelo. HEADER | BODY | FOOTER");
      }
    } catch (Exception $e) {
      if($msg)$mensagem = __LINE__ . " | Erro -> " . $e->getMessage();
    }
    if($msg)echo $mensagem;
  }
}

//EXEMPLOS (Só usar ctrl c + ctrl v)
$time_start = microtime(true);
//Parametros:
//Header é apenas um array onde cada valor representa uma coluna
$args["header"] = array('H', 'E', 'A', 'D', 'E', 'R');
//Body é apenas um array bi-dimensional | Aqui vai o for das adquirentes vindo da Api, linhas
$args["body"] = array();
for($r=0;$r<500;$r++){
  array_push($args["body"],array("Linha $r|1", "Linha $r|2", "Linha $r|3", "Linha $r|4", "Linha $r|5", "Linha $r|6", "Linha $r|7", "Linha $r|8"));
}
//Footer é apenas um array onde cada valor representa uma coluna
$args["footer"] = array('F', 'O', 'O', 'T', "E", "R");
/*
nome do arquivo, foi deito dessa forma para usar 
ANO,MES,DIA do inicio do arquivo, assim é possivel 
ordenar por mais recente ou mais antigo
*/
$args["diretorio"] = "exports/";
//$args["nome_arquivo"] = "exPagarMe";
//Chama o gerador
CsvGenerator::run($args,true);
//Tempo de execução
echo 'Tempo total: ' . (microtime(true) - $time_start);