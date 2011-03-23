$(document).ready(function(){
	$("img.feed").toggle(function(){
		$(this).attr("src","../static/images/butplus.gif");
		$(this).next("ul").slideUp("slow");
		}, function(){
			$(this).attr("src", "../static/images/butsub.gif");
			$(this).next("ul").slideDown("slow");
		}
	);
	$("p.remind").click(function(){
		$("#message").hide();
		$("#messagetext").show();
	});
	$("img.delete").click(function(){
		$("#message").hide();
		$("#messagetext").hide();
	});
	function commentsAction(){
		$("input#addComment").click(function(){
			var $comment = $("#comment").val();
			if($comment == ""){
				alert("评论不能为空");
				return false;
			}
			$.post("../comment/", {
				comment : $comment
			}, function(data, textStatus){
				$("#comment").attr("value", "");
				$("div.comments ul").append(data);
			});
			return false;
		});
	}
	function postsAction(){
		$("h2.flip").click(function(){
			$("#panall").slideToggle("slow");
		});
		$("h3.flip").click(function(){
			var $content = $(this).next("div.panel");
			var $opened = $("div.show");
			var $comments = $("div#rightsidebar");
			if ($content.hasClass("show")){
				$content.slideUp("slow").removeClass("show");
				$comments.empty().append('<p class="rightitle">评论</p>');
			} else {
				$opened.slideUp("slow").removeClass("show");
				$content.slideDown("slow").addClass("show");
				var $guid = $(this).attr("id");
				$.post("../comment/", { 
					guid : $guid 
				}, function(data, textStatus){
					$comments.empty().append(data);
					commentsAction();
				});
			}
		});
		$("img.star").click(
			function(){
				if($(this).attr("src")=="../static/images/starblank.png"){
					$(this).attr("src","../static/images/starfull.png");
				} else {
					$(this).attr("src","../static/images/starblank.png");
				}
		});
	}
	$(".choose a").click(function(){
		var $link = $(this).attr("href");
		$("#main").empty().append("<div class=\"loading_posts\">正在加载文章，请稍候....</div>");
		$("div#rightsidebar").empty().append('<p class="rightitle">评论</p>');
		$.post("../post/", {
			url : $link
		}, function(data, textStatus){
			$("#main").empty().append(data);
			postsAction();
		});
		return false;
	});
	$("#newfeed").click(function(){
		$(".feedform").show();
	});
	$("input.exit").click(function(){
		$(".feedform").hide();
		return false;
	});
	$("input.command_s").click(function(){
		$(".feedform").hide();
	});
});