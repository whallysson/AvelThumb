# AvelThumb
Classe para imagens em miniatura



AvelThumb.class [ HELPER ]
Versão 1.0
Classe para imagens em miniatura
@copyright (c) 2016, Whallysson Avelino - (whallyssonallain@gmail.com)


Como usar:
Ex.: http://seusite.com/upload/images/imagem.png?w=250&h=500&a=b&zc=2
$Tim = new AvelThumb;
echo $Tim->ImgCreate(http://seusite.com/upload/images/imagem.png?w=250&h=500&a=b&zc=2);
ou
echo $Tim->ImgCreate(upload/images/imagem.png?w=250&h=500&a=b&zc=2);