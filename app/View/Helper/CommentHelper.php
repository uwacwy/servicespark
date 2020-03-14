<?php

class CommentHelper extends AppHelper
{
	public $helpers = array('Time', 'Form', 'Html');
	public function formatComments($comments, $event_id, $level_container = "ol", $comment_container = "li", $depth = 0)
	{
		$rtn = "";
		$rtn .= sprintf('<%s class="comments event-%s">', $level_container, $event_id);

		foreach($comments as $comment)
		{
			$classes = array(
				'comment',
				is_null($comment['ParentComment']['comment_id'])? 'root' : 'child',
				!empty($comment['children'])? 'parent' : ''
			);
			$delete_link = "";
			if( $comment['User']['user_id'] == AuthComponent::user('user_id') )
			{
				$delete_link = __(' (%s)', $this->Html->link('delete', array('volunteer' => true, 'controller' => 'events', 'action' => 'delete_comment', $comment['Comment']['comment_id']) ) );
			}
			$rtn .= sprintf('<%1$s class="%3$s" id="comment-%2$s">',
				$comment_container,
				$comment['Comment']['comment_id'],
				implode(' ', $classes)
			);

				$rtn .= sprintf('<div class="comment-meta">');
					$rtn .= sprintf('<img src="%1$s/avatar/%2$s?s=40" alt="%3$s avatar" class="user-gravatar">',
						( isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) )? 'https://secure.gravatar.com' : 'http://www.gravatar.com',
						md5($comment['User']['email']),
						h($comment['User']['full_name'])
					);
					$rtn .= sprintf('<div class="comment-author user-%1$s">%2$s <span class="at-username">(@%1$s)</span></div>', 
						$comment['User']['username'], 
						$comment['User']['full_name']
						);
					$rtn .= sprintf('<div class="comment-date">%s%s</div>',
						$this->Time->timeAgoInWords($comment['Comment']['created']),
						$delete_link
						);
				$rtn .= sprintf('</div>');
				$rtn .= sprintf('<div class="comment-body">%s</div>', nl2br($comment['Comment']['body']) );
				$rtn .= $this->commentForm($event_id, $comment['Comment']['comment_id'], __('reply to %s...', $comment['User']['first_name']));
				if( !empty($comment['children']) )
				{
					$rtn .= $this->formatComments($comment['children'], $event_id, $level_container, $comment_container);
				}

			$rtn .= sprintf('</%s>', $comment_container);
		}

		$rtn .= sprintf('</%s>', $level_container);

		return $rtn;
	}

	public function commentForm($event_id, $parent_id = null, $placeholder = "Leave a Comment...")
	{
		$rtn = "";
		if( !is_null($parent_id) )
		{
			$rtn .= sprintf('<div class="comment-reply-trigger inactive"><a href="#"><span class="when-active">cancel</span> reply</a></div>');
		}
		$rtn .= sprintf('<div class="comment-reply">');
			$rtn .= $this->Form->Create('Comment', array('url' => array('volunteer' => true, 'controller' => 'events', 'action' => 'comment', $event_id)) );
			if( !is_null($parent_id) ){
				$rtn .= $this->Form->input('Comment.parent_id', array('type' => 'hidden', 'value' => $parent_id));
			}
			$rtn .= $this->Form->input('Comment.body', array(
					'type' => 'textarea', 
					'id' => false,
					'class' => 'reply-body form-control',
					'rows' => 1,
					'label' => false, 
					'placeholder' => $placeholder
				)
			);
			$rtn .= $this->Form->End(array('label' => __('Submit Reply'), 'class' => 'btn btn-primary btn-sm') );
		$rtn .= sprintf('</div>');

		return $rtn;
	}
}