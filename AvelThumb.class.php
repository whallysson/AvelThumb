<?php

/**
 * AvelThumb.class [ HELPER ]
 * Versão 1.0
 * Classe para imagens em miniatura
 * @copyright (c) 2016, Whallysson Avelino - (whallyssonallain@gmail.com)
 *
 * Como usar:
 * Ex.: http://seusite.com/upload/images/imagem.png?w=250&h=500&a=b&zc=2
 * $Tim = new AvelThumb;
 * echo $Tim->ImgCreate(http://seusite.com/upload/images/imagem.png?w=250&h=500&a=b&zc=2);
 * ou
 * echo $Tim->ImgCreate(upload/images/imagem.png?w=250&h=500&a=b&zc=2);
 */
class AvelThumb {

    private $File;
    private $Name;
    private $Url;
    private $Time;

    /** IMAGE UPLOAD */
    private $Image;

    /** RESULTSET */
    private $Result;
    private $Error;

    /** DIRETÓTIOS */
    private $DocRoot;
    private $Folder;
    private $MarcaDagua;
    private static $BaseDir;

    /** PROPERTIES */
    private $Wfull = 1200; // Tamanho maximo da largura da imagem caso não seja setada
    private $Wbase = 500; // Tamanho padrão da largura da imagem caso não seja setada
    private $Width;
    private $Height;
    private $Zc;
    private $A;

    /**
     * Verifica e cria o diretório padrão de cache no sistema!<br>
     * <b>../{$this->Folder}/</b>
     */
    function __construct($BaseDir = null, $Time = null) {
        $this->Folder = ( (string) $BaseDir ? $BaseDir : 'cache_img');
        $this->Time = ( $Time ? $Time : 604800); // 7 dias (padrão)
        $this->DocRoot = (stripos(dirname(__DIR__), '_app') !== false ? str_replace(array('\_app', '_app'), '', dirname(__DIR__)) : dirname(__DIR__) );
        self::$BaseDir = $this->DocRoot . "/{$this->Folder}/";
        if (!file_exists(self::$BaseDir) && !is_dir(self::$BaseDir)):
            mkdir(self::$BaseDir, 0777);
        endif;

        $this->existeFileCache();
    }

    /**
     * <b>Busca a Imagem:</b> Procura a imagem na pasta de cache
     * Caso não ache, é criada uma e retorna a sua url
     */
    public function ImgCreate($Url, $MarcaDagua = null) {
        $this->MarcaDagua = ( $MarcaDagua ? $MarcaDagua : null );
        $this->Url = $this->DocRoot . str_replace($this->UrlBase . '/', '', $Url);

        $ParseUrl = parse_url($this->Url);
        $this->File['query'] = (isset($ParseUrl['query']) ? $ParseUrl['query'] : null);

        $Exp = explode('.', substr(strrchr($ParseUrl['path'], '/'), 1));
        $this->Name = $Exp[0];
        $this->File['ext'] = (isset($Exp[1]) ? $Exp[1] : null);
		
        // Caso a imagem não exista ele cria no imagem na pasta cache
        $this->NoImage();

        // Limpa o Cache
        $this->CleanCache();

        // Seta o nome
        $this->setFileName();

        if ($this->ImgCache()) {
            return $this->UrlBase . '/' . $this->ImgCache(); // Retorna a imagem cacheada caso já exista
        } else {
            $this->Properties();
            $this->ImgSize();

            if (strstr($this->File["type"], 'image/')):
                $this->UploadImage();
                return $this->UrlBase . '/' . $this->getResult();
            endif;
        }
    }

    /**
     * <b>Verificar Upload:</b> Executando um getResult é possível verificar se o Upload foi executado ou não. Retorna
     * uma string com o caminho e nome do arquivo ou FALSE.
     * @return STRING  = Caminho e Nome do arquivo ou False
     */
    public function getResult() {
        return $this->Result;
    }

    /**
     * <b>Obter Erro:</b> Retorna um array associativo com um code, um title, um erro e um tipo.
     * @return ARRAY $Error = Array associatico com o erro
     */
    public function getError() {
        return $this->Error;
    }

    /*
     * ***************************************
     * **********  PRIVATE METHODS  **********
     * ***************************************
     */
	
	private function UrlBase() {
		$protocolo = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http');
		$dominio = $_SERVER['HTTP_HOST'];
		$url = $protocolo . '://' . $dominio;

		return $url;
	}

    // Cria no imagem na pasta cache
    private function NoImage() {
        if (empty($this->Name)) {
            $FileName = 'no_image.jpg';
            if (!file_exists(self::$BaseDir . $FileName)) {
                $ImgData = base64_decode("/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAJYAlgDAREAAhEBAxEB/8QAHgABAAICAwEBAQAAAAAAAAAAAAcJBggCAwUEAQr/xABREAABBAECAwUFBQMHBwgLAAAAAQIDBAUGEQcSIQgTMUFRFBUiYXEjMlJigRZCciQzU4KRkqElNDV0g5OUJkZzorHB0fA2Q0VjZXWElaO08f/EABQBAQAAAAAAAAAAAAAAAAAAAAD/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwD+3AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA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AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD/2Q==");
                $file = fopen(self::$BaseDir . $FileName, 'w+');
                fwrite($file, $ImgData);
                fclose($file);

                $time = time() + 31536000;
                touch(self::$BaseDir . $FileName, $time);
            }

            $this->Url = $this->UrlBase . "/{$this->Folder}/{$FileName}" . (!empty($this->File['query']) ? "?{$this->File['query']}" : null );
            $ParseUrl = parse_url($this->Url);
            $this->Name = substr(strrchr($ParseUrl['path'], '/'), 1);
            $this->File['ext'] = mb_strtolower(strrchr($this->Name, '.'));
        }
    }

    //Verifica e monta o nome dos arquivos tratando a string!
    private function setFileName() {
        $this->Name = $this->Name . '-' . md5($this->File['ext'] . $this->File['query']);
        $FileName = Check::Name($this->Name) . ".{$this->File['ext']}";
        $this->Name = mb_strtolower($FileName);
    }
    
    private function existeFileCache(){
        if (!file_exists(self::$BaseDir . 'cacheTime.txt')) {
            $SiteMapCheck = fopen(self::$BaseDir . 'cacheTime.txt', "w+");
            fwrite($SiteMapCheck, date('Y-m-d'));
            fclose($SiteMapCheck);
        }
    }

    // Apaga as imagens em cache 
    private function CleanCache() {
        $this->existeFileCache();
        
        $CacheTime = self::$BaseDir . 'cacheTime.txt';
        if (filemtime($CacheTime) < (time() - $this->Time)) { 
            $files = glob(self::$BaseDir . '*');
            if ($files) {
                $timeAgo = time() - $this->Time;
                foreach ($files as $file) {
                    if (filemtime($file) < $timeAgo) {
                        unlink($file);
                    }
                }

                clearstatcache();
            }
        }
    }

    // Verifica se a imagem já está cacheada
    private function ImgCache() {
        $Image = self::$BaseDir . $this->Name;
        if (file_exists($Image) && !is_dir($Image)):
            return "{$this->Folder}/{$this->Name}";
        else:
            return false;
        endif;
    }

    // Verifica o tamanho da imagem enviada
    private function ImgSize() {
        $Src = ( strrpos($this->Url, '?') ? substr($this->Url, 0, strrpos($this->Url, '?')) : $this->Url);
        $Tam = getimagesize($Src);
        $this->File['type'] = $Tam['mime'];
        $this->File['src'] = $Src;

        $x = $Tam[0];
        $y = $Tam[1];

        // Deixa a imagem em proporção da largura
        if (!empty($this->Width)) {
            $this->Width = $this->Width;
            $this->Height = (!empty($this->Height) ? $this->Height : ($this->Width * $y) / $x);
        } elseif (empty($this->Width) && !empty($this->Height)) {
            // Deixa a imagem em proporção da altura
            $this->Height = $this->Height;
            $this->Width = ($this->Height * $x) / $y;
        } else {
            // Deixa a imagem nas proporções setadas na base (500px largura)
            $this->Width = ( $this->Wbase < $x ? $this->Wbase : $x );
            $this->Height = ($this->Width * $y) / $x;
        }
    }

    // Seta as propriedades da imagem
    private function Properties() {
        parse_str($this->File['query']);
        $this->Width = ((int) isset($w) ? $w : null);
        $this->Height = ((int) isset($h) ? $h : null);
        $this->Zc = ((int) isset($zc) ? $zc : 1);
        $this->A = (isset($a) ? $a : 'c');
    }

    /* Realiza o upload de imagens redimensionando a mesma!
     * a -> Alinhamento / Posicionamento de Corte (Ex.: imagem.jpg?a=b)
     * a=c : position in the center (default)
     * a=t : align top
     * a=tr : align top right
     * a=tl : align top left
     * a=b : align bottom
     * a=br : align bottom right
     * a=bl : align bottom left
     * a=l : align left
     * a=r : align right
     * 
     * zc -> Zoom & Corte (Ex.: imagem.jpg?zc=2)
     * zc=0 : Redimensionar para Ajustar dimensões especificadas (sem corte)
     * zc=1 : Cortar e redimensionar para melhor ajustar as dimensões (default)
     * zc=2 : Redimensionar proporcionalmente para ajustar a imagem inteira em dimensões especificadas e adicionar bordas, se necessário
     * zc=3 : Redimensionar proporcionalmente ajustando o tamanho da imagem dimensionada para que não haja lacunas nas bordas
     */

    protected function UploadImage() {
        $Transparency = null;
        switch ($this->File['type']):
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $this->Image = imagecreatefromjpeg($this->File['src']);
                break;
            case 'image/png':
            case 'image/x-png':
                $this->Image = imagecreatefrompng($this->File['src']);
                $Transparency = 1;
                break;
            default :
                $this->Image = null;
                break;
        endswitch;

        if (!$this->Image):
            $this->Result = false;
            $this->Error = 'Tipo de arquivo inválido, envie imagens JPG ou PNG!';
        else:
            $x = imagesx($this->Image);
            $y = imagesy($this->Image);
            $ImageX = $this->Width;
            $ImageH = $this->Height;
            $origin_x = 0;
            $origin_y = 0;

            // Reduz e adiciona bordas
            if ($this->Zc == 3) {
                $FinalHeight = $y * ($ImageX / $x);

                if ($FinalHeight > $ImageH) {
                    $ImageX = $x * ($ImageH / $y);
                } else {
                    $ImageH = $FinalHeight;
                }
            }

            $NewImage = imagecreatetruecolor($ImageX, $ImageH);
            imagealphablending($NewImage, false);

            // Verifica se a variavel "Transparency" é verdadeira, para deixar o fundo transparente
            if (!empty($Transparency)):
                $colorTransparent = imagecolorallocatealpha($NewImage, 0, 0, 0, 127);
                imagefill($NewImage, 0, 0, $colorTransparent);
            else:
                $white = imagecolorallocate($NewImage, 255, 255, 255);
                imagefill($NewImage, 0, 0, $white);
            endif;

            imagesavealpha($NewImage, true);

            // scale down and add borders
            if ($this->Zc == 2) {
                $FinalHeight = $y * ($ImageX / $x);

                if ($FinalHeight > $ImageH) {
                    $origin_x = $ImageX / 2;
                    $ImageX = $x * ($ImageH / $y);
                    $origin_x = round($origin_x - ($ImageX / 2));
                } else {
                    $origin_y = $ImageH / 2;
                    $ImageH = $FinalHeight;
                    $origin_y = round($origin_y - ($ImageH / 2));
                }
            }

            if ($this->Zc > 0) {
                $src_x = $src_y = 0;
                $src_w = $x;
                $src_h = $y;

                $cmp_x = $x / $ImageX;
                $cmp_y = $y / $ImageH;

                // calculate x or y coordinate and width or height of source
                if ($cmp_x > $cmp_y) {
                    $src_w = round($x / $cmp_x * $cmp_y);
                    $src_x = round(($x - ($x / $cmp_x * $cmp_y)) / 2);
                } else if ($cmp_y > $cmp_x) {
                    $src_h = round($y / $cmp_y * $cmp_x);
                    $src_y = round(($y - ($y / $cmp_y * $cmp_x)) / 2);
                }

                // positional cropping!
                if ($this->A) {
                    if (strpos($this->A, 't') !== false) {
                        $src_y = 0;
                    }
                    if (strpos($this->A, 'b') !== false) {
                        $src_y = $y - $src_h;
                    }
                    if (strpos($this->A, 'l') !== false) {
                        $src_x = 0;
                    }
                    if (strpos($this->A, 'r') !== false) {
                        $src_x = $x - $src_w;
                    }
                }

                imagecopyresampled($NewImage, $this->Image, $origin_x, $origin_y, $src_x, $src_y, $ImageX, $ImageH, $src_w, $src_h);
            } else {
                imagecopyresampled($NewImage, $this->Image, 0, 0, 0, 0, $ImageX, $ImageH, $x, $y);
            }
            
            
            // Aplicado Marca D'agua
            if (!empty($this->MarcaDagua)):
                $marca = imagecreatefrompng($this->MarcaDagua);
                $marcax = imagesx($marca);
                $marcay = imagesy($marca);

                $localx = $ImageX - ($marcax + 20);
                $localy = $ImageH - ($marcay + 20);

                imagecopyresampled($NewImage, $marca, $localx, $localy, 0, 0, $marcax, $marcay, $marcax, $marcay);
            endif;



            switch ($this->File['type']):
                case 'image/jpg':
                case 'image/jpeg':
                case 'image/pjpeg':
                    imagejpeg($NewImage, self::$BaseDir . $this->Name, 90);
                    break;
                case 'image/png':
                case 'image/x-png':
                    imagepng($NewImage, self::$BaseDir . $this->Name, floor(90 * 0.09));
                    break;
            endswitch;

            if (!$NewImage):
                $this->Result = false;
                $this->Error = 'Tipo de arquivo inválido, envie imagens JPG ou PNG!';
            else:
                $this->Result = "{$this->Folder}/{$this->Name}";
                $this->Error = null;
            endif;

            imagedestroy($this->Image);
            imagedestroy($NewImage);
        endif;
    }

}
