window.onload = () => {
    document.getElementById("LoginButton").onclick = () => {
        fetch("http://" + window.location.host + "/api/user/login",{
            mode: "cors",
            headers : {
                "Access-Control-Allow-Origin": "*"
            },
            body: JSON.stringify({
                Username: document.getElementById("username").value,
                Password: document.getElementById("password").value
            }),
            method: "POST"
        }).then(function (response){
            if(response.status === 200){
                return response.json();
            }else{
                alert("login in failed");
                location.reload();
            }
        }).then(function (response){
            localStorage.setItem("token_browsergames", response.key);
            localStorage.setItem("username_browsergames", document.getElementById("username").value);
            location.replace("http://" + window.location.host + "?page=0");
        });
    }
}