$(document).ready(function () {
  let currentPostId = null;

  // Handle like button click
  $(".like-btn").click(function () {
    const postId = $(this).data("post-id");
    reactToPost(postId, "like");
  });

  // Handle dislike button click
  $(".dislike-btn").click(function () {
    const postId = $(this).data("post-id");
    reactToPost(postId, "dislike");
  });

  // Handle share button click
  $(".share-btn").click(function () {
    currentPostId = $(this).data("post-id");
  });

  // Handle share with caption button click
  $("#shareWithCaptionBtn").click(function () {
    const caption = $("#captionInput").val();
    if (caption.trim() === "") {
      alert("Please enter a caption.");
      return;
    }

    sharePost(currentPostId, caption);
  });

  // Function to send reaction data to the server
  function reactToPost(postId, type) {
    $.ajax({
      url: "handle_reaction.php",
      method: "POST",
      data: {
        post_id: postId,
        type: type,
      },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.status === "success") {
          // Update like and dislike counts
          $(`.like-btn[data-post-id="${postId}"]`).text(
            `👍 ${data.like_count}`
          );
          $(`.dislike-btn[data-post-id="${postId}"]`).text(
            `👎 ${data.dislike_count}`
          );
        } else {
          alert(data.message);
        }
      },
      error: function () {
        alert("An error occurred. Please try again.");
      },
    });
  }

  // Function to share a post with a caption
  function sharePost(postId, caption) {
    $.ajax({
      url: "share_post.php",
      method: "POST",
      data: {
        post_id: postId,
        caption: caption,
      },
      success: function (response) {
        const data = JSON.parse(response);
        if (data.status === "success") {
          alert(data.message);
          // Close the modal
          $("#captionModal").modal("hide");
          // Reload the feed or update the UI
          location.reload();
        } else {
          alert(data.message);
        }
      },
      error: function () {
        alert("An error occurred. Please try again.");
      },
    });
  }

  // Handle comment form submission
  $(".comment-form").submit(function (e) {
    e.preventDefault();
    const form = $(this);
    const postId = form.find('input[name="post_id"]').val();
    const content = form.find('textarea[name="content"]').val();
    const parentCommentId = form.find('input[name="parent_comment_id"]').val();

    $.ajax({
      url: "add_comment.php",
      method: "POST",
      data: {
        post_id: postId,
        content: content,
        parent_comment_id: parentCommentId,
      },
      success: function (response) {
        // Clear the form
        form.find('textarea[name="content"]').val("");

        // Reload the comments section
        loadComments(postId);
      },
      error: function () {
        alert("An error occurred. Please try again.");
      },
    });
  });

  // Function to load comments for a post
  function loadComments(postId) {
    $.ajax({
      url: "fetch_comments.php",
      method: "GET",
      data: { post_id: postId },
      success: function (response) {
        $("#comments-" + postId).html(response);
      },
      error: function () {
        alert("Failed to load comments.");
      },
    });
  }
});
