export function getCsrfToken() {
  let token = sessionStorage.getItem("csrf_token");
  if (!token) {
    token = crypto.randomUUID();
    sessionStorage.setItem("csrf_token", token);
  }
  return token;
}
