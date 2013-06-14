<?php
ini_set('display_errors', '1');
require_once 'DoodleAPI.php';

/* Specify your datas */

/* The options */
$options = array();

/* IN 2 DAYS */
$options[] = DoodleData::createDateOption(time() + (86400 * 2));
$options[] = DoodleData::createDateOption(time() + (86400 * 2), true);
$options[] = DoodleData::createDateOption(time() + (86400 * 2), true, time() + (86400 * 3));

/* the initiator */
$initiator = DoodleData::createInitiator("John Doe", "test@example.com");

/* The Poll */
$poll = DoodleData::writePoll(
                DoodleData::DOODLE_TYPE_DATE, true, "Doodle Sample", "This is some test", $options, $initiator
);
$write_comment1 = DoodleData::writeComment("user_1", "This is my first comment");
$write_comment2 = DoodleData::writeComment("user_2", "This is a second comment");
$write_comment3 = DoodleData::writeComment("user_3", "At least, the last comment");

$write_participant1 = DoodleData::writeAnswer("user_18", array(1, 0, 0));
$write_participant2 = DoodleData::writeAnswer("user_37", array(1, 0, 1));
$write_participant3 = DoodleData::writeAnswer("user_42", array(0, 0, 0));
?>

<h1> The Doodle Poll</h1>
<textarea><?php echo $poll; ?></textarea>
<br/>
<?php
$dapi = new DoodleAPI("vcxlgj6enaprf3iilj1pgqvamg2rtrbj", "pq8beypq6ze68jfjrfdo5zguzalw6kzo");
?>

<b>Create a Poll</b><br/>
<?php
try {
    $result = $dapi->createPoll($poll);
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
<br/>
<?php
$pollID = $result['Content-Location'];
$pollAdmin = $result['X-DoodleKey'];

echo "Link to vote <a href='http://www.doodle.com/{$pollID}'>http://www.doodle.com/{$pollID}</a><br/>";
echo "Link to administrate <a href='http://www.doodle.com/{$pollID}{$pollAdmin}/admin'>http://www.doodle.com/{$pollID}{$pollAdmin}/admin</a><br/>";
?>
<hr/>
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<b>Adding comments</b><br/>
<?php
try {
    $comment = $dapi->addComment($pollID, $write_comment1);
    $comment = $dapi->addComment($pollID, $write_comment2);
    $comment = $dapi->addComment($pollID, $write_comment3);
    echo "succeed adding 3 comments";
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
<hr/>


<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<b>Add Participants</b><br/>
<?php
try {
    $participant = $dapi->addParticipant($pollID, $write_participant1);
    $participant = $dapi->addParticipant($pollID, $write_participant2);
    $participant = $dapi->addParticipant($pollID, $write_participant3);
    echo "succeed adding 3 users";
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
<hr/>
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->

<b>Get the Poll</b><br/>
<?php
try {
    $result = $dapi->getPoll($pollID, $pollAdmin);

    echo "<textarea>" . print_r($result, true) . "</textarea>";
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
<hr/>

<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<b>Update Participants</b><br/>
<?php
$rwp2 = DoodleData::writeAnswer("user_37", array(1, 1, 1));
try {
    $participant = $dapi->updateParticipant($pollID, $pollAdmin, $result->participants->participant[1]->id, $rwp2);
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
<hr/>
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<b>Remove Comment</b><br/>
<?php
$rwp2 = DoodleData::writeAnswer("user_37", array(1, 1, 1));
try {
    $comment_id = $result->comments->comment[2]->id;
    $comment = $dapi->deleteComment($pollID, $pollAdmin, $comment_id);
    echo "succeed";
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
<hr/>
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<b>Remove Participants</b><br/>
<?php
try {
    $pid = $result->participants->participant[2]->id;
    $participant = $dapi->deleteParticipant($pollID, $pollAdmin, $pid);
    echo "succeed";
} catch (Exception $e) {
    echo $e->getMessage();
}
?>

<hr/>
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<b>Update Poll</b><br/>
<?php
try {
    $options[] = DoodleData::createDateOption(time() + 10 * 86400);
    $poll_update = DoodleData::writePoll(
                    DoodleData::DOODLE_TYPE_DATE, true, "Doodle Sample UPDATED", "This is the updated Option", $options, $initiator);
    $up_poll = $dapi->updatePoll($pollID, $pollAdmin, $poll_update);

    echo "succeed";
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
<hr/>
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->

<b>Get the Poll</b><br/>
<?php
try {
    $result = $dapi->getPoll($pollID, $pollAdmin);

    echo "<textarea>" . print_r($result, true) . "</textarea>";
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
<hr/>

<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->

<b>Delete the Poll</b><br/>

<?php
// remove first closing tag comment to enable Delete Poll function
/* */echo "disabled"; /*/
try {
    $del = $dapi->deletePoll($pollID, $pollAdmin);
    echo "succeed";
} catch (Exception $e) {
    echo $e->getMessage();
}
/**/
?>
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->
<!--------------------------------------------------------------->