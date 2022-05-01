<?php
namespace Csv\Reader;
use \Exception as Exception;

interface defaults
{
    //Função principal obrigatória, criar listagem das pastas
    public static function getFiles();
    //Função principal obrigatória, para capturar dados de 1 arquivo
    public static function getFileByNameFile();
}
class CsvReader implements defaults
{
    private static $filter;
    private static $order = 0;
    private static $diretorio;
    private static $nomeArquivo;
    private static $type;
    private static $separador;

    //filtra os verdadeiros arquivos
    private static function filterFiles($files)
    {
        unset($files[array_search('..', $files)]); 
        unset($files[array_search('.', $files)]);   

        if (self::$filter) $files = array_filter($files, function ($v, $k) {
            if (strpos($v, self::$filter) != null) return true;
        }, ARRAY_FILTER_USE_BOTH);

        
        //acrescenta informacoes importantes
        $newFiles = [];
        foreach ($files as $k => $v) {
            $newFiles[$k]["id"] = $k;
            $newFiles[$k]["file"] = $v;
            $newFiles[$k]["directory"] = self::$diretorio;
            $newFiles[$k]["modified"] = filemtime(self::$diretorio.$v);
            $newFiles[$k]["created_at"] = filectime(self::$diretorio.$v);
        }
        $files = $newFiles;

        if (self::$type == "json") $files = json_encode($files);

        return $files;
    }

    //Função para ler arquivos dentro do diretorio especificado.
    public static function getFiles($diretorio = "", $o = 0,$type = "array", $filter = "")
    {
        self::$type = $type;
        self::$filter = $filter;
        self::$diretorio = $diretorio;
        
        //Caso for decrescente troca a variavel order para 1
        $o === "desc" ? self::$order = 1 : self::$order = 0;
        //Scaneia a conforme o parametro
        try {
            $files = scandir(self::$diretorio, self::$order);
            //Remove dados desnecessarios e substitui a váriavel antiga
        } catch (Exception $e) {
            throw new Exception('Não foi possivel ler o diretório.');
        }
        $files = self::filterFiles($files, $filter);
        return $files;
    }

    //Função para ler arquivo e extrair dados.
    public static function getFileByNameFile($diretorio = "", $nomeArquivo = "", $type = "array", $separador=";")
    {
        self::$diretorio = $diretorio;
        self::$nomeArquivo = $nomeArquivo;
        self::$type = $type;
        self::$separador = $separador;
        try {
            $fp = fopen(self::$diretorio . self::$nomeArquivo, 'r');
            if ($fp) {
                while (!feof($fp)){
                    $theFile[] = explode(self::$separador, fgets($fp,9999));
                 }
                    if(self::$type=="json")$theFile=json_encode($theFile);
            } else {
                throw new Exception('Não foi possivel ler o arquivo.');
            }
            fclose($fp);
        } catch (Exception $e) {
            echo __LINE__ . " | Erro -> " . $e->getMessage();
        }
        if(!isset($thefile))$thefile[]=null;
        return $theFile;
    }
}

 

//EXEMPLOS (Só usar ctrl c + ctrl v e passar valor direto sem variavel se quiser.)
$time_start = microtime(true);

/*
Exemplo de listagem por diretório
  asc = mais antigos no topo | desc = mais recentes no topo (conforme o modelo do CsvGenerator)
  Filters: é aplicado em todo arquivo, por exemplo, é possivel trazer os arquivos da mesma data ou pelo nome definido no arquivo
*/

$list = CsvReader::getFiles($diretorio = "exports/", $order = "desc", $type = "json", $filter = ".csv");
var_dump($list);

/*
Exemplo de retorno dos dados do csv por nome do arquivo
*/

$getCsv = CsvReader::getFileByNameFile($diretorio = "exports/", $nomeArquivo = "20210907194823__exPagarMe__BrData=07-09-2021_19-48-23.csv", $type = "json", $separador=";");
var_dump($getCsv);

//Tempo de execução
echo 'Tempo total: ' . (microtime(true) - $time_start);

//DICA use JSON como parametro, e pegue os valores com file_get_contents(), é mais rápido.