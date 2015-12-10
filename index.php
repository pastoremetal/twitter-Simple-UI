<?php
	require_once 'twitterRequest.php';
	$twitterUi = new twitterUi();
?>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta content="IE=edge" http-equiv="X-UA-Compatible">
		<meta content="width=device-width, initial-scale=1" name="viewport">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Twitter Simple UI</title>
		<link type="text/css" rel="stylesheet" href="library/bootstrap-3.1.1-dist/css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="style.css">
		<script src='library/angular.min.js'></script>
	</head>
	<body ng-app='Twitter'>
		<div class='container' ng-controller='RequestingCtrl'>
			<div class='row'>
				<div class='col-xs-12 col-md-6 col-md-push-3 bgWhite padding25px roundCornerB shadowB'>
					<form action="" name="userSelect" id="userSelect">
						<span class='col-xs-6 col-md-3'>
							<input type='radio' name='tpUser' id='tpUser' value='1' ng-model='tp' ng-change="changeUser('me')" /><label for='tpUser'>Eu</label>
						</span>
						<span class='col-xs-6 col-md-9'>
							<input type='text' name='selUser' id='selUser' class='col-xs-12' ng-change="changeUser('an')" ng-model='selUser' placeholder='Digite o nome de um usuário' />
						</span>
						<input type='button' value='VER TWEETS' ng-click="requestTweets()" class="col-xs-12 bgBlue colorWhite noBorder txBold bgLightBlueHover" />
						<input type='button' value='NOVO TWEET' ng-click="postTweet()" class="col-xs-12 bgGreen colorWhite noBorder txBold bgLightGreenHover mTop10Px" />
						<textarea name='nTweet' id="nTweet" class='mTop10Px h75Px col-xs-12' maxlength='140' ng-model='nTweet' ng-show='setPost'></textarea>
						<input type='button' value='ENVIAR' ng-click="sendTweet()" class="col-xs-12 col-md-2 bgGreen colorWhite noBorder txBold bgLightGreenHover roundCornerB"  ng-show='setPost' />
					</form>
				</div>
			</div>
			<div class='row'>
				<div class='col-xs-12 col-md-6 col-md-push-3 bgWhite padding25px roundCornerT roundCornerB shadowB top40Px'>
					<div class='col-xs-12 roundCornerT noPadding ofHidden shadowB mTop20Px'  ng-repeat="tweet in tweets track by $index">						
						<div class='col-xs-2 noPadding'><img src='{{tweet.user.profile_image_url}}' class='img-responsive col-xs-12 noPadding' /></div>
						<div class='col-xs-8 paddingT15px'>{{tweet.text}}</div>						
						<!--<div class='col-xs-12 h25Px bgLightGray txBold'>{{tweet.created_at}}</div>-->
					</div>
				</div>
		</div>
	</body>
</html>

<script>
	var twt = angular.module('Twitter', []);
	twt.controller('RequestingCtrl', function($scope, $http){
		$scope.who = 'me';
		$scope.tp = 1;
		$scope.selUser;
		$scope.tweets = [];
		$scope.setPost = 0;
		$scope.nTweet;
		
		$scope.postTweet = function(){$scope.setPost = ($scope.setPost==0)?1:0;};
		
		$scope.changeUser = function(def){
			if(def=='me'){
				if($scope.tp==1){
					$scope.who = 'me';
					$scope.selUser = '';
				}else{
					$scope.who = $scope.selUser;
				}
			}else{
				if($scope.selUser!=''){
					$scope.tp = 0;
					$scope.who = $scope.selUser;
				}
			}
		};

		$scope.requestTweets = function(){
			$http({
				method: 'POST',
				url: 'twitterRequest.php',
				data: "req=tweets&who="+$scope.who,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).success(function(response){
				angular.forEach(response, function(el, i){
					if(i=='httpstatus' || i=='rate'){return false;}
					$scope.tweets[i] = el;
				});
			});
		};

		$scope.sendTweet = function(){
			$http({
				method: 'POST',
				url: 'twitterRequest.php',
				data: "req=send&tweet="+$scope.nTweet,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).success(function(response){
				if(response.sent==true){
					$scope.nTweet = '';
					$scope.setPost = 0;
					$scope.changeUser('me');
					$scope.requestTweets();
				}
			});
		};
		
		$scope.changeUser('me');
		$scope.requestTweets();
	});
</script>