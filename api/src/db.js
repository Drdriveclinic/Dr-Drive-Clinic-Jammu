import mysql from 'mysql2/promise';
import { env } from './config/env.js';

let pool = null;
let isConnected = false;

try {
  pool = mysql.createPool({
    host: env.DB_HOST,
    port: env.DB_PORT,
    user: env.DB_USER,
    password: env.DB_PASS,
    database: env.DB_NAME,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
    dateStrings: true
  });
} catch (err) {
  console.warn('[DB Pool Init Warning]:', err.message);
}

export async function getPool() {
  return pool;
}

export async function query(sql, params = []) {
  if (!pool) {
    throw new Error('Database pool is not initialized');
  }
  const [rows] = await pool.execute(sql, params);
  return rows;
}

export async function checkConnection() {
  if (!pool) return false;
  try {
    const conn = await pool.getConnection();
    conn.release();
    isConnected = true;
    return true;
  } catch (err) {
    isConnected = false;
    return false;
  }
}

export async function withTransaction(callback) {
  if (!pool) {
    throw new Error('Database pool is not initialized');
  }
  const connection = await pool.getConnection();
  await connection.beginTransaction();
  try {
    const result = await callback(connection);
    await connection.commit();
    return result;
  } catch (err) {
    await connection.rollback();
    throw err;
  } finally {
    connection.release();
  }
}

export default {
  getPool,
  query,
  checkConnection,
  withTransaction
};
