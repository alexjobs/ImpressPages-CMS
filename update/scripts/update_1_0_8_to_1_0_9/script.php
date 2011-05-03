<?php
/**
 * @package   ImpressPages
 * @copyright Copyright (C) 2011 ImpressPages LTD.
 * @license   GNU/GPL, see ip_license.html
 */
namespace update_1_0_7_to_1_0_8;

if (!defined('CMS')) exit;

require_once('translations.php');

class Script {
  var $deleteFiles;
  var $addFiles;
  var $deleteFolders;
  var $addFolders;

  var $stepCount;
  var $curStep;
  var $curAction;


  public function __construct($stepCount, $curStep, $curAction) {
    $this->deleteFolders = array();
    $this->deleteFolders[] = 'install';
    $this->deleteFolders[] = 'ip_cms';
    $this->deleteFolders[] = 'ip_libs';


    $this->deleteFiles = array();
    $this->deleteFiles[] = 'admin.php';
    $this->deleteFiles[] = 'index.php';
    $this->deleteFiles[] = 'ip_backend_frames.php';
    $this->deleteFiles[] = 'ip_backend_worker.php';
    $this->deleteFiles[] = 'ip_cron.php';
    $this->deleteFiles[] = 'ip_license.html';
    $this->deleteFiles[] = 'sitemap.php';

    $this->addFolders = array();
    $this->addFolders[] = 'ip_cms';
    $this->addFolders[] = 'ip_libs';
    $this->addFolders[] = 'ip_plugins';

    $this->addFiles = array();
    $this->addFiles[] = 'admin.php';
    $this->addFiles[] = 'index.php';
    $this->addFiles[] = 'ip_backend_frames.php';
    $this->addFiles[] = 'ip_backend_worker.php';
    $this->addFiles[] = 'ip_cron.php';
    $this->addFiles[] = 'ip_license.html';
    $this->addFiles[] = 'sitemap.php';

    $this->stepCount = $stepCount;
    $this->curStep = $curStep;
    $this->curAction = $curAction;
  }

  public function getActionsCount() {
    return 4;
  }

  public function process () {
    global $htmlOutput;
    global $navigation;

    $answer = '';


    switch ($this->curAction) {
      default:
      case 1:
        $answer .= $this->filesToDelete();
      break;
      case 2:
        $answer .= $this->filesToUpload();
      break;
      case 3:
        $answer .= $this->updateRobots();
      break;
      case 4:
        $answer .= $this->updateDatabase();
      break;
    }


    return $answer;
  }


  public function updateRobots() {
    global $navigation;
    global $htmlOutput;

    $answer = '';

    $robotsFile = '../robots.txt';
    if (is_writable($robotsFile)) {

      $data = file($robotsFile, FILE_IGNORE_NEW_LINES);
      $newData = '';
      foreach($data as $dataKey => $dataVal) {
        $tmpVal = $dataVal;
        $tmpVal = trim($tmpVal);
        $tmpVal = str_replace('User-Agent:', 'User-agent:', $tmpVal);

        $tmpVal =  preg_replace('/^User-Agent:(.*)/', 'User-agent:${0}', $tmpVal);
        $tmpVal =  preg_replace('/^Disallow: \/ip_cms$/', 'Disallow: /ip_cms/', $tmpVal);
        $tmpVal =  preg_replace('/^Disallow: \/ip_configs$/', 'Disallow: /ip_configs/', $tmpVal);
        $tmpVal =  preg_replace('/^Disallow: \/update$/', 'Disallow: /update/', $tmpVal);
        $tmpVal =  preg_replace('/^Disallow: \/install$/', 'Disallow: /install/', $tmpVal);
        $tmpVal =  preg_replace('/^Sitemap:(.*)/', 'Sitemap: '.BASE_URL.'sitemap.php', $tmpVal);
        $newData .= $tmpVal."\n";
      }

      file_put_contents($robotsFile, $newData);

      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
    } else {
      $answer .= MAKE_ROBOTS_WRITEABLE;
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= "<br/>";
      $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
    }

    return $answer;
  }



  public function needToDelete() {
    $answer = false;
    if($this->curStep == 1 && !isset($_SESSION['process'][1]['deleted'])) {
      foreach ($this->deleteFolders as $folder){
        if (is_dir('../'.$folder) ) {
          $answer = true;
        }
      }
      foreach ($this->deleteFiles as $file){
        if (is_file('../'.$file) ) {
          $answer = true;
        }
      }

      if ($answer == false) {
        $_SESSION['process'][1]['deleted'] = true;
      }
    }
    return $answer;
  }

  public function needToUpload() {
    $answer = false;
    if($this->curStep == $this->stepCount && !isset($_SESSION['process'][1]['uploaded'])) {
      foreach ($this->addFolders as $folder){
        if (!is_dir('../'.$folder) ) {
          $answer = true;
        }
      }
      foreach ($this->addFiles as $file){
        if (!is_file('../'.$file) ) {
          $answer = true;
        }
      }

      if ($answer == false) {
        $_SESSION['process'][1]['uploaded'] = true;
      }
    }
    return $answer;
  }

  public function filesToDelete() {
    global $navigation;
    global $htmlOutput;

    $answer = '';

    $tableFolders = array();

    foreach ($this->deleteFolders as $folder){
      if (is_dir('../'.$folder) ) {
        $tableFolders[] = '/'.$folder.'/';
        $tableFolders[] = '';
      }
    }


    if (sizeof($tableFolders)) {
      $answer .= REMOVE_DIRECTORIES.$htmlOutput->table($tableFolders);
      $answer .= '<br/>';
    }



    $tableFiles = array();
    foreach ($this->deleteFiles as $file){
      if (is_file('../'.$file) ) {
        $tableFiles[] = '/'.$file;
        $tableFiles[] = '';
      }
    }

    if (sizeof($tableFiles)) {
      $answer .= REMOVE_FILES.$htmlOutput->table($tableFiles);
    }

    if ($this->needToDelete())
      $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
    else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
    }

    return $answer;
  }

  public function filesToUpload(){
    global $navigation;
    global $htmlOutput;

    $answer = '';

    $tableFolders = array();

    foreach ($this->addFolders as $folder){
      if (!is_dir('../'.$folder) ) {
        $tableFolders[] = '/'.$folder.'/';
        $tableFolders[] = '';
      }
    }


    if (sizeof($tableFolders)) {
      $answer .= UPLOAD_DIRECTORIES.$htmlOutput->table($tableFolders);
      $answer .= '<br/>';
    }



    $tableFiles = array();
    foreach ($this->addFiles as $file){
      if (!is_file('../'.$file) ) {
        $tableFiles[] = '/'.$file;
        $tableFiles[] = '';
      }
    }

    if (sizeof($tableFiles)) {
      $answer .= UPLOAD_FILES.$htmlOutput->table($tableFiles);
    }

    if ($this->needToUpload())
      $answer .= $htmlOutput->button(IP_NEXT, $navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction()));
    else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript(), $navigation->curAction() + 1));
    }
    return $answer;
  }





  public function updateDatabase() {
    global $navigation;
    global $htmlOutput;

    $answer = '';
    if (\Db_100::getSystemVariable('version') != '1.0.9') {


      if ($this->curStep == $this->stepCount){
        \Db_100::setSystemVariable('version','1.0.8');
      }
    }

    if ($this->curStep == $this->stepCount) {
      header("location: ".$navigation->generateLink($navigation->curStep() + 1));
    } else {
      header("location: ".$navigation->generateLink($navigation->curStep(), $navigation->curScript() + 1));
    }

    return $answer;
  }






}
