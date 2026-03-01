document.addEventListener("DOMContentLoaded", function () {

    const audio = document.getElementById("frp-audio");
    const playBtn = document.getElementById("frp-play");
    const playShape = document.getElementById("frp-play-shape");
    const popupBtn = document.getElementById("frp-popup");
    const trackEl = document.getElementById("frp-current-track");
    const coverImg = document.querySelector(".frp-cover img");
    const volumeSlider = document.getElementById("frp-volume");

    if (!audio || !playBtn) return;

    let lastTitle = "";

    /* ===============================
       Play / Pause
    =============================== */

    playBtn.addEventListener("click", function () {

        if (audio.paused) {

            audio.play();

            // Pause Icon (zwei Balken)
            if (playShape) {
                playShape.setAttribute(
                    "points",
                    "9,8 11,8 11,16 9,16 13,8 15,8 15,16 13,16"
                );
            }

        } else {

            audio.pause();

            // Play Icon (Dreieck)
            if (playShape) {
                playShape.setAttribute(
                    "points",
                    "10,8 17,12 10,16"
                );
            }
        }
    });

    /* ===============================
       Volume
    =============================== */

    if (volumeSlider) {

        audio.volume = volumeSlider.value;

        volumeSlider.addEventListener("input", function () {
            audio.volume = this.value;
        });
    }

    /* ===============================
       Popup
    =============================== */

    if (popupBtn && typeof frpData !== "undefined" && frpData.popup_url) {

        popupBtn.addEventListener("click", function () {

            window.open(
                frpData.popup_url,
                "FRP Popup",
                "width=350,height=450,left=20,top=20,resizable=no,scrollbars=no"
            );

        });
    }

    /* ===============================
       Titel vom Server laden
    =============================== */

    function fetchTitle() {

        fetch(frpData.ajax_url + "?action=frp_get_title")
            .then(response => response.json())
            .then(data => {

                if (data.success && data.data) {

                    const newTitle = data.data.trim();

                    if (newTitle !== lastTitle) {

                        lastTitle = newTitle;
                        trackEl.textContent = newTitle;

                        fetchCoverArt(newTitle);
                    }
                }

            })
            .catch(() => {
                // Fehler → nichts tun
            });
    }

    /* ===============================
       CoverArt über iTunes API
    =============================== */

    function fetchCoverArt(trackString) {

        if (!coverImg) return;

        if (!trackString.includes(" - ")) return;

        const parts = trackString.split(" - ");
        const artist = parts[0].trim();
        const title = parts[1].trim();

        const query = encodeURIComponent(artist + " " + title);

        fetch("https://itunes.apple.com/search?term=" + query + "&entity=song&limit=1")
            .then(response => response.json())
            .then(data => {

                if (data.resultCount > 0) {

                    let artwork = data.results[0].artworkUrl100;

                    // größere Version
                    artwork = artwork.replace("100x100bb", "300x300bb");

                    coverImg.src = artwork;

                } else {
                    // Fallback
                    if (coverImg.dataset.fallback) {
                        coverImg.src = coverImg.dataset.fallback;
                    }
                }

            })
            .catch(() => {
                if (coverImg.dataset.fallback) {
                    coverImg.src = coverImg.dataset.fallback;
                }
            });
    }

    /* ===============================
       Initial + Intervall
    =============================== */

    fetchTitle();
    setInterval(fetchTitle, 15000);

});