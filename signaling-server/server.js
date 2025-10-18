const WebSocket = require("ws");
const wss = new WebSocket.Server({ port: 8080 });

const clients = new Map(); // Store clients by user ID

wss.on("connection", (ws) => {
  console.log("Client connected");

  ws.on("message", (message) => {
    const data = JSON.parse(message);

    if (data.type === "register") {
      // Register the client with a user ID
      clients.set(data.userId, ws);
      console.log(`User ${data.userId} registered`);
    } else if (data.type === "signal") {
      // Route the signal to the target user
      const targetClient = clients.get(data.targetUserId);
      if (targetClient) {
        targetClient.send(JSON.stringify(data));
      } else {
        console.error(`Target user ${data.targetUserId} not found`);
      }
    }
  });

  ws.on("close", () => {
    console.log("Client disconnected");
    // Remove the client from the map
    for (const [userId, client] of clients.entries()) {
      if (client === ws) {
        clients.delete(userId);
        break;
      }
    }
  });
});

console.log("Signaling server running on ws://localhost:8080");
