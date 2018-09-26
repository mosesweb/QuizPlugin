<div class="quiz-item">
<?php 
$args = array(
    'post_type'   => 'attachment',
    'order' => 'DESC',
    'orderby' => 'ID',
    //'post_status' => 'private',
    'post_status' => array('inherit', 'publish', 'private'),
    'meta_query'  => array(
        array(
            'key'     => 'quiz_map_id',
            'value'   => $row["groupid"]
        )
    )
);
$image_is_author_made = false;
$query = new WP_Query($args);
$image = "";
while ( $query->have_posts() ) : $query->the_post();
$image = wp_get_attachment_image_src(get_the_ID(), 'medium')[0];
if(get_the_author_id() == $userid && get_post_status() == 'private')
        $image_is_author_made = true;
   // $image = wp_get_attachment_url();
endwhile;
$posts = $query->posts;

if($image != "")
{
    if($image_is_author_made)
    {
        echo "<div class=\"quiz-header-img img-in-review\">
        <div class=\"img-in-review-text\">Your picture is in review</div>
        <img class='' src=\" ". $image . "\" /></div>";
    }
    else
    {
        echo "<div class=\"quiz-header-img\"><img src=\" ". $image . "\" /></div>";
    }
}
?>
<div class="inner-quiz-item-area">
    <h1 class="quiz-item-header"><a href="/quiz/<?php print $row["slug_name"]; ?>"><?php print $row["groupname"]; ?></a></h1>
    <div class="quiz-description"><p><?php print $row["groupdescription"]; ?></p></div>
    <div class="quiz-author"><p>Created by <?php print $row["author"]; ?></p></div>
</div>
</div>