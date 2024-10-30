<?php
$children = $this->getChildren();
$found = false;
foreach ($children as $child) 
{
  if (1==$child->getValue('active'))
  {
    if (0==$this->getValue('protected') ||
         (isset($_SESSION['mqgpassword']) 
          && (string)$_SESSION['mqgpassword'] == $child->getValue('password1')
         )
        )
    {
      // Active Gallery setzen
      MQGallery::$activeGallery = $child;
 
   
      // Gallery-params updaten
      $child->updateParams($params);

      echo $child->getView($child->getValue('defaultview'),$params);
      $found = true;
      break;

    }
  }
}
if (false===$found)
{
  // Kein Firstchild gefunden -> index ausgeben
  echo $this->getView('index',$params);
}


