app.get('/callback', async (req, res) => {
  const code = req.query.code;

  if (!code || typeof code !== 'string' || code.length < 10) {
    return res.send(`
      <section style="
        font-family: 'Segoe UI', sans-serif;
        background: #0e0e10;
        color: #ff4e4e;
        padding: 40px;
        text-align: center;
        border-radius: 12px;
        margin: 80px auto;
        max-width: 600px;
        box-shadow: 0 0 20px rgba(255, 0, 0, 0.2);
      ">
        <h1 style="font-size: 28px;">❌ Verificación fallida</h1>
        <p style="font-size: 16px; color: #ccc; margin: 10px 0;">
          Discord no envió el parámetro <code>code</code> o está incompleto.
        </p>
        <p style="margin-top: 15px; font-size: 13px; color: #666;">Sistema Abyssus · Error OAuth2</p>
      </section>
    `);
  }

  try {
    const tokenResponse = await axios.post(
      'https://discord.com/api/oauth2/token',
      new URLSearchParams({
        client_id: process.env.CLIENT_ID,
        client_secret: process.env.CLIENT_SECRET,
        grant_type: 'authorization_code',
        code,
        redirect_uri: process.env.REDIRECT_URI?.trim(),
      }).toString(),
      {
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      }
    );

    const accessToken = tokenResponse.data.access_token;

    // ✅ Pantalla de carga animada
    res.send(`
      <html lang="es">
      <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Abyssus — Verificando</title>
        <style>
          body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            background: radial-gradient(circle at center, #0f0f10, #050505);
            color: #00ffb3;
            font-family: 'Segoe UI', sans-serif;
            overflow: hidden;
          }
          .logo {
            font-size: 40px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #00ffb3;
            animation: glow 2s infinite alternate;
          }
          @keyframes glow {
            from { text-shadow: 0 0 10px #00ffb3; }
            to { text-shadow: 0 0 25px #00ffb3; }
          }
          .loader {
            margin-top: 20px;
            border: 4px solid rgba(0, 255, 179, 0.2);
            border-top: 4px solid #00ffb3;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
          }
          @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
          }
          .text {
            margin-top: 25px;
            font-size: 16px;
            color: #bbb;
            animation: fade 1.5s ease-in-out infinite alternate;
          }
          @keyframes fade {
            from { opacity: 0.5; }
            to { opacity: 1; }
          }
        </style>
        <script>
          setTimeout(() => {
            window.location.href = "/?token=${accessToken}";
          }, 3000);
        </script>
      </head>
      <body>
        <div class="logo">ABYSSUS</div>
        <div class="loader"></div>
        <div class="text">Verificando tu sesión con Discord...</div>
      </body>
      </html>
    `);
  } catch (error) {
    const errorMsg = error.response?.data?.error_description || error.message || 'Error desconocido';
    res.send(`
      <section style="
        font-family: 'Segoe UI', sans-serif;
        background: #1c1c1c;
        color: #ff4e4e;
        padding: 40px;
        text-align: center;
        border-radius: 12px;
        margin: 80px auto;
        max-width: 600px;
        box-shadow: 0 0 20px rgba(255, 0, 0, 0.2);
      ">
        <h1 style="font-size: 26px;">❌ Error en la autenticación</h1>
        <p style="font-size: 16px; color: #ccc; margin: 10px 0;">
          ${errorMsg}
        </p>
        <p style="margin-top: 15px; font-size: 13px; color: #666;">Sistema Abyssus · sesión fallida</p>
      </section>
    `);
  }
});




















