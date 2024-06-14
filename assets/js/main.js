function checkTokenExpiry() {
	alert('DSFDS');
  const token = localStorage.getItem('token');
  if (!token) return true;

  const payload = JSON.parse(atob(token.split('.')[1]));
  const expiry  = payload.exp * 1000;
  return Date.now() >= expiry;
}

function logout() {
  fetch('/api/logout', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${localStorage.getItem('token')}`
    }
  }).then(() => {
    localStorage.removeItem('token');
    window.location.href = '/login';
  });
}

setInterval(() => {
  alert('hhh');
  if (checkTokenExpiry()) {
	logout();
  }else{
    alert('not expire');
  }
}, 60000); // Check every minute
