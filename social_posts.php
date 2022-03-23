<?php

// date en français
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');


//Facebook SDK for PHP
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

require_once __DIR__ .'/vendor/facebook/graph-sdk/src/Facebook/autoload.php';
require_once __DIR__ .'/vendor/facebook/graph-sdk/src/Facebook/Exceptions/FacebookResponseException.php';
require_once __DIR__ .'/vendor/facebook/graph-sdk/src/Facebook/Exceptions/FacebookSDKException.php';
require_once __DIR__ .'/vendor/facebook/graph-sdk/src/Facebook/Helpers/FacebookRedirectLoginHelper.php';


function facebook_feed()
{
    //données facebook developers
    $appFbId = "2599113913752265";
    $appFbSecret = "320a5d4e46995f4ba6f4178c9d8a8804";
    $accessTokenFb = 'EAAk74V2PWskBACcHTkJa9ME5eBMEFZBfOV6GcGGaFZBUpwRfzmoZCwvH1GwXvdsYiPeI2rnvfHZBZCWcqVCJaSE6sTkcHAYmRgsBp7CIMpUc8Uc6UWuMK0u3MbJInZA03MePhJrZCHwwdlz2O4ROLyZCc6X6rFRn6wYANyBgzJlVmAZDZD';
    $idFb = '102650954830599';

    //données instagram
    $appInstaId = "2660149137580603";
    $appInstaSecret = "e3b2be4390b617ba815389753b5a1736";
    $accessTokenInsta = 'IGQVJYdmM5NVdVdkRiLVJfUlFfOUcyWTJEZAEQ3cjFRNlRXMHNwWmtmajN2R09SQkVLaVlKZA011WWRnR2Rxbk5OcGJlTTdldTBDbFlQc2hNMGdYNTBOQnNuSGk1ZAlBicUtIYjZAueWF3';
    $idInsta = '17841438503486478';

    //une façon de récuperer l'idInsta si on ne le connait pas
    $urlInsta = "https://graph.facebook.com/102650954830599?fields=connected_instagram_account,instagram_business_account&access_token=".$accessTokenFb;
    $resultInsta = file_get_contents($urlInsta);
    $decodedInsta = json_decode($resultInsta, true);
    if (!empty($decodedInsta["instagram_business_account"])) {
        $idInsta = $decodedInsta["instagram_business_account"]["id"];
    } else {
        $idInsta = $decodedInsta["connected_instagram_account"]["id"];
    }

    $fb = new Facebook([
      'app_id' => $appFbId,
      'app_secret' => $appFbSecret,
      'default_graph_version' => 'v7.0'
    ]);

    $insta = new Facebook([
      'app_id' => $appFbId,
      'app_secret' => $appFbSecret,
      'default_graph_version' => 'v7.0'
    ]);

    // recupere les posts Facebook et Instagram
    $postDataFb = "";
    $postDataInsta = "";
    try {
        $userPostsFb = $fb->get("/$idFb/feed", $accessTokenFb);
        $postBodyFb = $userPostsFb->getDecodedBody();
        $postDataFb = $postBodyFb["data"];

        $userPostsInsta = $insta->get("/$idInsta/media", $accessTokenFb);
        $postBodyInsta = $userPostsInsta->getDecodedBody();
        $postDataInsta = $postBodyInsta["data"];
    } catch (FacebookResponseException $e) {
        // display error message
        exit();
    } catch (FacebookSDKException $e) {
        // display error message
        exit();
    }

    // FACEBOOK
    $facebookPosts = array();
    $idFbSplit=array();

    for ($i=0;$i<count($postDataFb);$i++) {
        $idFbPost = $postDataFb[$i]["id"];
        $split = explode("_", $idFbPost);
        $idFbSplit[$i]= $split[1];
        $url = "https://graph.facebook.com/".$idFbPost."?fields=attachments,shares,full_picture,likes.limit(0).summary(true),comments.limit(0).summary(true)&access_token=".$accessTokenFb;
        $result = file_get_contents($url);
        $decoded = json_decode($result, true);

        $facebookPosts[$i]["class"]= "wh_facebook";
        $facebookPosts[$i]["id_post"]= $idFbSplit[$i];
        $facebookPosts[$i]["message"]= $postDataFb[$i]["message"];
        $facebookPosts[$i]["date"] = strftime("%d %B %Y", strtotime($postDataFb[$i]["created_time"]));
        $facebookPosts[$i]["date_unix"] = strtotime($postDataFb[$i]["created_time"]);
        $facebookPosts[$i]["image"] = $decoded["full_picture"];
        $facebookPosts[$i]["like"] = $decoded["likes"]["summary"]["total_count"];
        $facebookPosts[$i]["comment"] = $decoded["comments"]["summary"]["total_count"];
        $facebookPosts[$i]["share"] = $decoded["shares"]["count"];
        $facebookPosts[$i]["video"] = $decoded["attachments"]["data"][0]["media"]["source"];
    }

    // INSTAGRAM
    $instaPosts = array();
    for ($i=0;$i<count($postDataInsta);$i++) {
        $idInstaPost = $postDataInsta[$i]["id"];
        $urlPost = "https://graph.facebook.com/".$idInstaPost."?fields=permalink,like_count,comments_count,caption,media_type,media_url,username,timestamp&access_token=".$accessTokenFb;
        $resultPost = file_get_contents($urlPost);
        $decodedPost = json_decode($resultPost, true);

        $instaPosts[$i]["class"]= "wh_instagram";
        $instaPosts[$i]["message"]= $decodedPost["caption"];
        $instaPosts[$i]["type"]= $decodedPost["media_type"];
        $instaPosts[$i]["date"]= strftime("%d %B %Y", strtotime($decodedPost["timestamp"]));
        $instaPosts[$i]["date_unix"] = strtotime($decodedPost["timestamp"]);
        $instaPosts[$i]["image"] = $decodedPost["media_url"];
        $instaPosts[$i]["permalink"] = $decodedPost["permalink"];
        $instaPosts[$i]["like"] = $decodedPost["like_count"];
        $instaPosts[$i]["comment"] = $decodedPost["comments_count"];
    }

    // on fusionne les 2 tableaux Fb et Insta puis on trie par date
    $postsRS = array_merge($facebookPosts, $instaPosts);

    // Define the custom sort function
    function custom_sort($a, $b)
    {
        return $a['date_unix']<$b['date_unix'];
    }
    // Sort the multidimensional array
    usort($postsRS, "custom_sort"); ?>


<div class="grid">
<!-- ajouter autant de grid-col qu'on veut de de post sur une ligne-->
<div class="grid-col grid-col--1"></div>
<div class="grid-col grid-col--2"></div>
<div class="grid-col grid-col--3"></div>
<div class="grid-col grid-col--4"></div>
<div class="grid-col grid-col--5"></div>

<?php
for ($i=0; $i < count($postsRS); $i++) {
        if ($postsRS[$i]["class"] == "wh_facebook") { ?>
<div class="grid-item">
  <?php
  if (strpos($postsRS[$i]["video"], 'https://www.youtube.com')=== 0) {
      echo '<iframe src='.$postsRS[$i]["video"].' frameborder="0" allowfullscreen></iframe>';
  } elseif (!empty($postsRS[$i]["video"])) {
      echo '<video controls> <source src='.$postsRS[$i]["video"].'> type="video/mp4" ></video> ';
  } else {
      echo '<img src='.$postsRS[$i]["image"].'>';
  }
  ?>

  <div class="card-content">
    <p class="card-date"><?php echo $postsRS[$i]["date"]; ?></p>
    <p><?php if (!empty($postsRS[$i]["message"])) {
      echo $postsRS[$i]["message"];
  } ?></p>
  </div>

  <div class="card-footer">
    <a href="https://www.facebook.com/<?php echo $idFb ?>/posts/<?php echo $postsRS[$i]["id_post"] ?>"><small
      class="change"><i class="far fa-thumbs-up"></i>
      <?php if (!empty($postsRS[$i]["like"])) {
      echo $postsRS[$i]["like"];
  } else {
      echo '0';
  }?></small></a>
      <a href="https://www.facebook.com/<?php echo $idFb ?>/posts/<?php echo $postsRS[$i]["id_post"] ?>"><small
        class="change"><i class="far fa-share-square"></i>
        <?php if (!empty($postsRS[$i]["share"])) {
      echo $postsRS[$i]["share"];
  } else {
      echo '0';
  }?></small></a>
        <a href="https://www.facebook.com/<?php echo $idFb ?>/posts/<?php echo $postsRS[$i]["id_post"] ?>"><small
          class="change"><i class="far fa-comment"></i>
          <?php if (!empty($postsRS[$i]["comment"])) {
      echo $postsRS[$i]["comment"];
  } else {
      echo '0';
  } ?></small></a>
          <div class="dropdown">
            <a><small class="change"><i class="fas fa-share-alt-square social-menu"></i></small></a>
              <div class="dropdown-content">
                <a
                href="http://www.facebook.com/sharer.php?u=https://www.facebook.com/<?php echo $idFb ?>/posts/<?php echo $postsRS[$i]["id_post"] ?>"><i
                class="fab fa-facebook-f"></i>Facebook</a>
                <a
                href="https://twitter.com/intent/tweet?url=https://www.facebook.com/<?php echo $idFb ?>/posts/<?php echo $postsRS[$i]["id_post"] ?>"><i
                class="fab fa-twitter"></i>Twitter</a>
                <a href="http://www.instagram.com/"><i class="fab fa-instagram"></i>Instagram</a>
                <a
                href="https://www.pinterest.com/pin/create/button/?url=https://www.facebook.com/<?php echo $idFb ?>/posts/<?php echo $postsRS[$i]["id_post"]?>&media=<?php echo $postsRS[$i]["image"] ?>">
                <i class="fab fa-pinterest-p"></i>Pinterest</a>
                <a
                href="https://www.linkedin.com/cws/share?url=https://www.facebook.com/<?php echo $idFb ?>/posts/<?php echo $postsRS[$i]["id_post"] ?>"><i
                class="fab fa-linkedin-in"></i>Linkedin</a>
                <a
                href="mailto:?subject=&body=https://www.facebook.com/<?php echo $idFb ?>/posts/<?php echo $postsRS[$i]["id_post"] ?>"><i
                class="fas fa-envelope"></i>Email</a>
              </div>
            </div>
          </div>
        </div>

      <?php } else { ?>

        <div class="grid-item">
          <?php
          if ($postsRS[$i]["type"]=="IMAGE") {
              echo '<img src='.$postsRS[$i]["image"].'>';
          } else {
              echo '<video controls> <source  src='.$postsRS[$i]["image"].'> type="video/mp4" ></video>';
          }?>

            <div class="card-content">
              <p class="card-date"><?php echo $postsRS[$i]["date"]; ?></p>
              <p><?php if (!empty($postsRS[$i]["message"])) {
              echo $postsRS[$i]["message"];
          } ?></p>
            </div>
            <div class="card-footer">
              <a href="<?php echo $postsRS[$i]["permalink"] ?>"><small
                class="change"><i class="far fa-heart"></i>
                <?php if (!empty($postsRS[$i]["like"])) {
              echo $postsRS[$i]["like"];
          } else {
              echo '0';
          }?></small></a>
                <a href="<?php echo $postsRS[$i]["permalink"] ?>"><small
                  class="change"><i class="far fa-comment"></i>
                  <?php if (!empty($postsRS[$i]["comment"])) {
              echo $postsRS[$i]["comment"];
          } else {
              echo '0';
          } ?></small></a>

                  <div class="dropdown">
                    <a><small class="change"><i class="fas fa-share-alt-square social-menu"></i></small></a>
                      <div class="dropdown-content">
                        <a
                        href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $postsRS[$i]["permalink"] ?>"><i
                        class="fab fa-facebook-f"></i>Facebook</a>
                        <a
                        href="https://twitter.com/intent/tweet?text=Voir%20cette%20photo%20Instagram%20de&url=<?php echo $postsRS[$i]["permalink"] ?>"><i
                        class="fab fa-twitter"></i>Twitter</a>
                        <a
                        href="http://www.instagram.com/"><i class="fab fa-instagram"></i>Instagram</a>
                        <a
                        href="mailto:?subject=&body=<?php echo $postsRS[$i]["permalink"] ?>"><i
                        class="fas fa-envelope"></i>Email</a>
                      </div>
                    </div>
                </div>
              </div>
              <?php
            }
    } ?>
</div>
      <?php
}
