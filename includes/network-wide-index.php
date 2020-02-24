<?php

namespace Pagansites\Core;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . '/get-index-table-name.php';
require_once plugin_dir_path( __FILE__ ) . '/get-current-permalink.php';

function add_post_to_index( $post_id, $post ) {
	global $wpdb;

	// get the correct table prefix for the top site to use for the
	// index table (as it only exists at the top)
	$index_table = get_index_table_name();

	$current_blog_id = get_current_blog_id();
	$index_post_id = null;

	$index_post_id = $wpdb->get_var(
		"SELECT id FROM " . $index_table
		. " WHERE blog_id = " . $current_blog_id . " AND"
		. " post_id = " . $post_id);

	if ($index_post_id) {
		$update = true;
	} else {
		$update = false;
	}
	// If this is a revision, don't process
	if ( wp_is_post_revision( $post_id ) )
		return;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	//$post_url = get_permalink( $post_id );

	$timestamp = date('Y-m-d H:i:s');
	$current_blog_id = get_current_blog_id();

	// date (created or modified) gets added later
	$index_data = array(
		'blog_id' => $current_blog_id,
		'post_id' => $post_id,
		'user_id' => $post->post_author,
		'post_title' => $post->post_title,
		'post_slug' => $post->post_name,
		'post_excerpt' => $post->post_excerpt,
		'post_index' => remove_stop_words( sanitize_textarea_field( $post->post_content ) ),
		'post_body' => sanitize_textarea_field($post->post_content),
		'post_guid' => get_current_permalink($current_blog_id, $post_id));

	// accounts for date (created or modifed) even though not yet in the
	// above data array
	$index_format = array('%d','%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

	if ($update) {
		// add the modified date to the data array
		$index_data = array_merge($index_data, array('post_modified' => $timestamp));

		$index_where = [ 'id' => $index_post_id ];
		$index_where_format = [ '%d' ];

		$updated_id = $wpdb->update($index_table, $index_data, $index_where, $index_format, $index_where_format );
		if ( false === $updated_id ) {
			// todo error
		}
	} else {
		// add the created date to the data array
		$index_data = array_merge($index_data, array('post_created' => $timestamp));

		$wpdb->insert($index_table, $index_data, $index_format);
		$inserted_id = $wpdb->insert_id;

	}




	$subject = 'A post has been updated';

}
add_action( 'publish_post', __NAMESPACE__.'\\add_post_to_index', 10, 2 );

//add_action( 'save_post', __NAMESPACE__.'\\add_post_to_index', 10, 2 );

function on_all_status_transitions( $new_status, $old_status, $post ) {
	// todo if post is unpublished, remove it from the index
	if ( $new_status != 'publish' ) {
		// A function to perform actions any time any post changes status.
	}
}
add_action(  'transition_post_status',  __NAMESPACE__.'\\on_all_status_transitions', 10, 3 );





function remove_stop_words($text) {
	$stop_words = <<< EOF
able,about,above,abroad,according,accordingly,across,actually,adj,after,afterwards,again,against,ago,ahead,ain't,all,allow,allows,almost,alone,along,alongside,already,also,although,always,am,amid,amidst,among,amongst,an,and,another,any,anybody,anyhow,anyone,anything,anyway,anyways,anywhere,apart,appear,appreciate,appropriate,are,aren't,around,as,a's,aside,ask,asking,associated,at,available,away,awfully,back,backward,backwards,be,became,because,become,becomes,becoming,been,before,beforehand,begin,behind,being,believe,below,beside,besides,best,better,between,beyond,both,brief,but,by,came,can,cannot,cant,can't,caption,cause,causes,certain,certainly,changes,clearly,c'mon,co,co.,com,come,comes,concerning,consequently,consider,considering,contain,containing,contains,corresponding,could,couldn't,course,c's,currently,dare,daren't,definitely,described,despite,did,didn't,different,directly,do,does,doesn't,doing,done,don't,down,downwards,during,each,edu,eg,eight,eighty,either,else,elsewhere,end,ending,enough,entirely,especially,et,etc,even,ever,evermore,every,everybody,everyone,everything,everywhere,ex,exactly,example,except,fairly,far,farther,few,fewer,fifth,first,five,followed,following,follows,for,forever,former,formerly,forth,forward,found,four,from,further,furthermore,get,gets,getting,given,gives,go,goes,going,gone,got,gotten,greetings,had,hadn't,half,happens,hardly,has,hasn't,have,haven't,having,he,he'd,he'll,hello,help,hence,her,here,hereafter,hereby,herein,here's,hereupon,hers,herself,he's,hi,him,himself,his,hither,hopefully,how,howbeit,however,hundred,i'd,ie,if,ignored,i'll,i'm,immediate,in,inasmuch,inc,inc.,indeed,indicate,indicated,indicates,inner,inside,insofar,instead,into,inward,is,isn't,it,it'd,it'll,its,it's,itself,i've,just,k,keep,keeps,kept,know,known,knows,last,lately,later,latter,latterly,least,less,lest,let,let's,like,liked,likely,likewise,little,look,looking,looks,low,lower,ltd,made,mainly,make,makes,many,may,maybe,mayn't,me,mean,meantime,meanwhile,merely,might,mightn't,mine,minus,miss,more,moreover,most,mostly,mr,mrs,much,must,mustn't,my,myself,name,namely,nd,near,nearly,necessary,need,needn't,needs,neither,never,neverf,neverless,nevertheless,new,next,nine,ninety,no,nobody,non,none,nonetheless,noone,no-one,nor,normally,not,nothing,notwithstanding,novel,now,nowhere,obviously,of,off,often,oh,ok,okay,old,on,once,one,ones,one's,only,onto,opposite,or,other,others,otherwise,ought,oughtn't,our,ours,ourselves,out,outside,over,overall,own,particular,particularly,past,per,perhaps,placed,please,plus,possible,presumably,probably,provided,provides,que,quite,qv,rather,rd,re,really,reasonably,recent,recently,regarding,regardless,regards,relatively,respectively,right,round,said,same,saw,say,saying,says,second,secondly,see,seeing,seem,seemed,seeming,seems,seen,self,selves,sensible,sent,serious,seriously,seven,several,shall,shan't,she,she'd,she'll,she's,should,shouldn't,since,six,so,some,somebody,someday,somehow,someone,something,sometime,sometimes,somewhat,somewhere,soon,sorry,specified,specify,specifying,still,sub,such,sup,sure,take,taken,taking,tell,tends,th,than,thank,thanks,thanx,that,that'll,thats,that's,that've,the,their,theirs,them,themselves,then,thence,there,thereafter,thereby,there'd,therefore,therein,there'll,there're,theres,there's,thereupon,there've,these,they,they'd,they'll,they're,they've,thing,things,think,third,thirty,this,thorough,thoroughly,those,though,three,through,throughout,thru,thus,till,to,together,too,took,toward,towards,tried,tries,truly,try,trying,t's,twice,two,un,under,underneath,undoing,unfortunately,unless,unlike,unlikely,until,unto,up,upon,upwards,us,use,used,useful,uses,using,usually,v,value,various,versus,very,via,viz,vs,want,wants,was,wasn't,way,we,we'd,welcome,well,we'll,went,were,we're,weren't,we've,what,whatever,what'll,what's,what've,when,whence,whenever,where,whereafter,whereas,whereby,wherein,where's,whereupon,wherever,whether,which,whichever,while,whilst,whither,who,who'd,whoever,whole,who'll,whom,whomever,who's,whose,why,will,willing,wish,with,within,without,wonder,won't,would,wouldn't,yes,yet,you,you'd,you'll,your,you're,yours,yourself,yourselves,you've,zero
EOF;

	$stop_words_array = explode(",", $stop_words);

	foreach ($stop_words_array as $stop_word) {
		$text = preg_replace('/\b' . $stop_word . '\b/u', '', strtolower($text));
	}

	$text = preg_replace('!\s+!', ' ', $text);

	return $text;

}
