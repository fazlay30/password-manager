import { useState } from 'react';
import Cookies from 'js-cookie';

export const useStorage = (storageType = 'localStorage') => {
  const [error, setError] = useState(null);

  // Check which storage to use
  const isLocalStorage = storageType === 'localStorage';

  const setItem = (key, value, options = {}) => {
    try {
      const serializedValue = JSON.stringify(value);

      if (isLocalStorage) {
        localStorage.setItem(key, serializedValue);
      } else {
        Cookies.set(key, serializedValue, options);
      }
    } catch (err) {
      setError(err);
    }
  };

  const getItem = (key) => {
    try {
      const data = isLocalStorage
        ? localStorage.getItem(key)
        : Cookies.get(key);

      return data ? JSON.parse(data) : null;
    } catch (err) {
      setError(err);
      return null;
    }
  };

  const removeItem = (key) => {
    try {
      if (isLocalStorage) {
        localStorage.removeItem(key);
      } else {
        Cookies.remove(key);
      }
    } catch (err) {
      setError(err);
    }
  };

  return { setItem, getItem, removeItem, error };
};
