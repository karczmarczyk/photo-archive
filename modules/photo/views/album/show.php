<?php
/* ładuję skrypty Angular */
use app\assets\AngularAsset;
AngularAsset::register($this);
/* ładuję Angular UI Bootstrap */
use app\assets\AngularUiBootstrapAsset;
AngularUiBootstrapAsset::register($this);
/* ładuję Angular Animate */
use app\assets\AngularAnimateAsset;
AngularAnimateAsset::register($this);
/* ładuję skrypty JqueryScroll */
use app\assets\JqueryScrollAsset;
JqueryScrollAsset::register($this);
/* ładuję tiles galery */
$this->registerCssFile($assetsPath.'/css/tiles-galery.css');
$this->registerJsFile($assetsPath.'/js/tiles-galery.js');
/* łąduję moduł never-ending-story */
$this->registerJsFile($assetsPath.'/js/ng-never-ending-story.js');
/* ładuję moduł tiles-gallery */
$this->registerJsFile($assetsPath.'/js/ng-tiles-gallery.js');
/* ładuję moduł imageonload */
$this->registerJsFile($assetsPath.'/js/ng-imageonload.js');
/* ładuję skrypt fullScreenMode*/
$this->registerJsFile($assetsPath.'/js/fullscreenmode.js');
?>
<div ng-app="myApp" ng-controller="photosCtrl">
    <div id="tiles-galery" class="col-lg-12 tiles-galery scrollbar-light" never-ending-story="nextPhotos()" jquery-scrollbar="jqueryScrollbarOptions">
        <div class="row tiles" ng-repeat="photo in photos" ng-include="'tile'"></div>
    </div>
    <div style="clear: both"></div>
    <div ng-if="loadingPage" class="loadnextpage">
        <?=yii\helpers\Html::img('\images\loadbar1.gif')?>
    </div>
    <div ng-if="end" class="end_tail">
        Koniec
    </div>
    <script type="text/ng-template" id="tile">
        <?=$this->render('_ng-tile');?>
    </script>
    <script type="text/ng-template" id="dialogContent">
        <?=$this->render('_ng-dialog-content');?>
    </script>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('.scrollbar-light').scrollbar();
    });
</script>

<script type="text/javascript">
    var album_id = <?=$id?>;
    
    /* Główny kontroler */
    var app = angular.module('myApp',['never-ending-story','tiles-gallery','ui.bootstrap','imageonload','ngAnimate']);
    app.controller('photosCtrl', function($scope, $http, $uibModal){
        $scope.page = 1;
        $scope.loadingPage = false;
        $scope.photos = [];
        $scope.end = false;
        
        $scope.gallery_width = document.getElementById('tiles-galery').clientWidth;
        
        $scope.loadPhotos = function(next) {
            $http.get('/photo/json/photos?album_id='+album_id+'&page='+$scope.page).then(function(response){      
                if (response.data==='') {
                    $scope.loadingPage = false;
                    $scope.end = true;
                    return;
                }
                var temp = [];
                var temp2 = [];
                var i = 0;
                angular.forEach(response.data, function(value, key){
                    i++;
                    temp[i] = value;
                    temp2[0] = temp;
                });
                if (next) {
                    $scope.photos.push(temp2);
                } else {
                    $scope.photos.push(temp2);
                    //$scope.nextPhotos();
                }
                $scope.loadingPage = false;
            });
        };
        
        $scope.nextPhotos = function() {
            if ($scope.end) {
                $scope.loadingPage = false;
                return;
            }
            $scope.loadingPage = true;
            $scope.page++;
            $scope.loadPhotos(1);
        };
        
        $scope.loadPhotos(0);
        
        /* DIALOG */
        $scope.open = function (photo) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'dialogContent',
                controller: 'ModalInstanceCtrl',
                size: 'full',
                resolve: {
                    photo: function () {
                        return photo;
                    }
                }
            });
        };
       
    });
    
    /* DIALOG KONTROLER */
    angular.module('myApp').controller('ModalInstanceCtrl', function ($scope, $uibModalInstance, $http, photo, $document) {
        $scope.dialogView = 'normal';
        $scope.photo = photo;
        $scope.visiblePhoto = true;
        $scope.showPhoto = function() {
            $scope.visiblePhoto = true;
        };
        $scope.close = function () {
            $uibModalInstance.dismiss('cancel');
        };
        $scope.goToNext = function (id) {
            $scope.visiblePhoto = false;
            $http.get('/photo/json/nextphoto?photo_id='+id).then(function(response){
                if (response.data==='') {
                    end = true;
                    return;
                }
                $scope.photo = response.data;
            });
        };
        $scope.goToPrevious = function (id) {
            $scope.visiblePhoto = false;
            $http.get('/photo/json/previousphoto?photo_id='+id).then(function(response){
                if (response.data==='') {
                    end = true;
                    return;
                }
                $scope.photo = response.data;
            });
        };
        $scope.tab = 1;
        $scope.openTab = function(tab) {
            $scope.tab = tab;
        };
        
        $document.bind("keydown keypress", function(event) {
            if(event.keyCode == 39) {
                $scope.goToNext($scope.photo.id);
            }
            if(event.keyCode == 37) {
                $scope.goToPrevious($scope.photo.id);
            }
        });
        
        $scope.ngToggleFullScreen = function () {
            toggleFullScreen();
            if ($scope.dialogView === 'fullscreen') {
                $scope.dialogView = 'normal';
            } else {
                $scope.dialogView = 'fullscreen';
            }
        };
        
        $scope.getDialogView = function () {
            return $scope.dialogView;
        };
    });
</script>