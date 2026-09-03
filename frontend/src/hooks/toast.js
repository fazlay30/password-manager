import { toast } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'

export function useToast() {
  const showToast = (message, type = 'info') => {
    switch (type) {
    case 'success':
      toast.success(message)
      break
    case 'error':
      toast.error(message)
      break
    case 'warning':
      toast.warning(message)
      break
    case 'info':
    default:
      toast.info(message)
      break
    }
  }

  return { showToast }
}
