window.onload = () => {
    document.getElementById("LoginButton").onclick = () => {
        window.location.replace("http://" + window.location.host + "?page=1");
    }
}