(function () {
    // Create floating button
    const bubble = document.createElement("div");
    bubble.id = "gps-chatbot-bubble";

    bubble.style.position = "fixed";
    bubble.style.bottom = "20px";
    bubble.style.right = "20px";
    bubble.style.width = "55px";
    bubble.style.height = "55px";
    bubble.style.background = "#436CFF";
    bubble.style.borderRadius = "50%";
    bubble.style.display = "flex";
    bubble.style.alignItems = "center";
    bubble.style.justifyContent = "center";
    bubble.style.boxShadow = "0 6px 20px rgba(0,0,0,0.25)";
    bubble.style.cursor = "pointer";
    bubble.style.zIndex = "9999";
    bubble.style.color = "white";
    bubble.style.fontSize = "26px";
    bubble.innerHTML = "💬";

    document.body.appendChild(bubble);

    // Create iframe chatbot window
    const iframe = document.createElement("iframe");
    iframe.src = "/chatbot_widget.html";
    iframe.style.position = "fixed";
    iframe.style.bottom = "90px";
    iframe.style.right = "20px";
    iframe.style.width = "330px";
    iframe.style.height = "440px";
    iframe.style.border = "none";
    iframe.style.borderRadius = "15px";
    iframe.style.boxShadow = "0 6px 20px rgba(0,0,0,0.25)";
    iframe.style.zIndex = "9998";
    iframe.style.display = "none";

    document.body.appendChild(iframe);

    // Toggle
    bubble.onclick = () => {
        iframe.style.display = iframe.style.display === "none" ? "block" : "none";
    };
})();
