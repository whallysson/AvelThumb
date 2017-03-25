# AvelThumb
Classe para imagens em miniatura

[![N|Solid](https://stc.pagseguro.uol.com.br/public/img/botoes/doacoes/209x48-doar-assina.gif)](https://pag.ae/bfh6JgF)


@copyright (c) 2016, Whallysson Avelino - (whallyssonallain@gmail.com)


### Como usar
Ex.: http://seusite.com/upload/images/imagem.png?w=250&h=500&a=b&zc=2
``` php
$Tim = new AvelThumb;
echo $Tim->ImgCreate(http://seusite.com/upload/images/imagem.png?w=250&h=500&a=b&zc=2);
ou
echo $Tim->ImgCreate(upload/images/imagem.png?w=250&h=500&a=b&zc=2);
```


### Upload e redimensionando de imagens
  a -> Alinhamento / Posicionamento de Corte (Ex.: imagem.jpg?a=b)
``` php
  a=c : position in the center (default)
  a=t : align top
  a=tr : align top right
  a=tl : align top left
  a=b : align bottom
  a=br : align bottom right
  a=bl : align bottom left
  a=l : align left
  a=r : align right
```

  zc -> Zoom & Corte (Ex.: imagem.jpg?zc=2)
``` php
  zc=0 : Redimensionar para Ajustar dimensões especificadas (sem corte)
  zc=1 : Cortar e redimensionar para melhor ajustar as dimensões (default)
  zc=2 : Redimensionar proporcionalmente para ajustar a imagem inteira em dimensões especificadas e adicionar bordas, se necessário
  zc=3 : Redimensionar proporcionalmente ajustando o tamanho da imagem dimensionada para que não haja lacunas nas bordas
```

[![N|Solid](https://stc.pagseguro.uol.com.br/public/img/botoes/doacoes/209x48-doar-assina.gif)](https://pag.ae/bfh6JgF)


