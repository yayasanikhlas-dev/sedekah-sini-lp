(function () {
  function formatTime(seconds) {
    if (!isFinite(seconds) || seconds < 0) {
      return "0:00";
    }
    var minutes = Math.floor(seconds / 60);
    var remain = Math.floor(seconds % 60);
    return minutes + ":" + (remain < 10 ? "0" : "") + remain;
  }

  function initVideo(root) {
    if (!root || root.getAttribute("data-sslp-ready") === "video") {
      return;
    }
    var video = root.querySelector("video");
    var button = root.querySelector(".sslp-video__play");
    if (!video || !button) {
      return;
    }
    root.setAttribute("data-sslp-ready", "video");

    function setPlaying(playing) {
      root.classList.toggle("is-playing", playing);
    }

    button.addEventListener("click", function () {
      if (video.paused) {
        var pending = video.play();
        if (pending && pending.catch) {
          pending.catch(function () {
            setPlaying(false);
          });
        }
        setPlaying(true);
      } else {
        video.pause();
        setPlaying(false);
      }
    });

    video.addEventListener("click", function () {
      if (!video.paused) {
        video.pause();
      }
    });
    video.addEventListener("pause", function () {
      setPlaying(false);
    });
    video.addEventListener("ended", function () {
      setPlaying(false);
      video.currentTime = 0;
    });
    video.addEventListener("play", function () {
      setPlaying(true);
    });
  }

  function initVoice(root) {
    if (!root || root.getAttribute("data-sslp-ready") === "voice") {
      return;
    }
    var audio = root.querySelector("audio");
    var button = root.querySelector(".sslp-voice__play");
    var time = root.querySelector(".sslp-voice__time");
    var fill = root.querySelector(".sslp-voice__fill");
    if (!audio || !button || !time || !fill) {
      return;
    }
    root.setAttribute("data-sslp-ready", "voice");

    var awaitingInteraction = false;
    var interactionEvents = ["click", "touchend", "keydown"];

    function cancelAutoplay() {
      awaitingInteraction = false;
      interactionEvents.forEach(function (eventName) {
        document.removeEventListener(eventName, retryAutoplay);
      });
    }

    function retryAutoplay(event) {
      if (!root.isConnected) {
        cancelAutoplay();
        return;
      }
      // Explicit player controls take precedence over pending autoplay.
      if (event.target && event.target.closest && event.target.closest("[data-sslp-voice]")) {
        cancelAutoplay();
        return;
      }
      if (event.isTrusted && awaitingInteraction) {
        cancelAutoplay();
        playAudio(false);
      }
    }

    function playAudio(autoplay) {
      function failed(error) {
        if (autoplay && error && error.name === "NotAllowedError" && root.isConnected) {
          awaitingInteraction = true;
          interactionEvents.forEach(function (eventName) {
            document.addEventListener(eventName, retryAutoplay);
          });
        }
      }
      try {
        var pending = audio.play();
        if (pending && pending.catch) {
          pending.catch(failed);
        }
      } catch (error) {
        failed(error);
      }
    }

    function paint() {
      var duration = audio.duration;
      var current = audio.currentTime || 0;
      if (audio.paused || !current) {
        time.textContent = formatTime(duration);
      } else {
        time.textContent = formatTime(current);
      }
      fill.style.width = duration ? (current / duration) * 100 + "%" : "0";
    }

    audio.addEventListener("loadedmetadata", paint);
    audio.addEventListener("timeupdate", paint);
    audio.addEventListener("play", function () {
      cancelAutoplay();
      document.querySelectorAll("[data-sslp-voice] audio").forEach(function (other) {
        if (other !== audio) {
          other.pause();
        }
      });
      root.classList.add("is-playing");
      button.setAttribute("aria-label", "Jeda audio");
    });
    audio.addEventListener("pause", function () {
      root.classList.remove("is-playing");
      button.setAttribute("aria-label", "Main audio");
      paint();
    });
    audio.addEventListener("ended", function () {
      root.classList.remove("is-playing");
      button.setAttribute("aria-label", "Main audio");
      audio.currentTime = 0;
      paint();
    });

    button.addEventListener("click", function () {
      cancelAutoplay();
      if (audio.paused) {
        playAudio(false);
      } else {
        audio.pause();
      }
    });

    paint();
    if (root.getAttribute("data-autoplay") === "yes") {
      playAudio(true);
    }
  }

  function boot(scope) {
    var base = scope || document;
    base.querySelectorAll(".sslp-video").forEach(initVideo);
    base.querySelectorAll("[data-sslp-voice]").forEach(initVoice);
  }

  function bindElementor() {
    if (!window.elementorFrontend || !elementorFrontend.hooks) {
      return;
    }
    elementorFrontend.hooks.addAction("frontend/element_ready/sslp-video.default", function ($scope) {
      initVideo($scope[0] ? $scope[0].querySelector(".sslp-video") : null);
    });
    elementorFrontend.hooks.addAction("frontend/element_ready/sslp-voice-note.default", function ($scope) {
      var node = $scope[0] ? $scope[0].querySelector("[data-sslp-voice]") : null;
      initVoice(node);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      boot(document);
    });
  } else {
    boot(document);
  }

  if (window.elementorFrontend && elementorFrontend.hooks) {
    bindElementor();
  } else {
    window.addEventListener("elementor/frontend/init", bindElementor);
  }
})();
