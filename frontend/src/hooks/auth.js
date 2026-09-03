import useSWR from 'swr'
import axios from '@/lib/axios'
import { useEffect } from 'react'
import { useParams, useRouter } from 'next/navigation'
import { useStorage } from './storage'

export const useAuth = ({ middleware, redirectIfAuthenticated } = {}) => {
  const router = useRouter()
  const params = useParams()
  const { setItem, getItem } = useStorage('localStorage')

  const { data: user, error, mutate, isValidating } = useSWR('/api/user', () =>
    axios
      .get('/api/user')
      .then(res => {
        setItem('user', res.data)
        return res.data
      })
      .catch(error => {
        if (error.response.status !== 409) throw error

        router.push('/verify-email')
      }),
  )

  const isLoading = !user && isValidating && !error

  const csrf = () => axios.get('/sanctum/csrf-cookie')

  const register = async ({ setErrors, ...props }) => {
    await csrf()

    setErrors([])

    axios
      .post('/register', props)
      .then(() => mutate())
      .catch(error => {
        if (error.response.status !== 422) throw error

        setErrors(error.response.data.errors)
      })
  }

  const login = async ({ setErrors, setStatus, ...props }) => {
    await csrf()

    setErrors([])
    setStatus(null)

    axios
      .post('/login', props)
      .then(() => {
        router.push(redirectIfAuthenticated || '/dashboard')
        mutate()
      })
      .catch(error => {
        if (error.response.status !== 422) throw error

        setErrors(error.response.data.errors)
      })
  }

  const forgotPassword = async ({ setErrors, setStatus, email }) => {
    await csrf()

    setErrors([])
    setStatus(null)

    axios
      .post('/forgot-password', { email })
      .then(response => setStatus(response.data.status))
      .catch(error => {
        if (error.response.status !== 422) throw error

        setErrors(error.response.data.errors)
      })
  }

  const resetPassword = async ({ setErrors, setStatus, ...props }) => {
    await csrf()

    setErrors([])
    setStatus(null)

    axios
      .post('/reset-password', { token: params.token, ...props })
      .then(response =>
        router.push('/login?reset=' + btoa(response.data.status)),
      )
      .catch(error => {
        if (error.response.status !== 422) throw error

        setErrors(error.response.data.errors)
      })
  }

  const resendEmailVerification = ({ setStatus }) => {
    axios
      .post('/email/verification-notification')
      .then(response => setStatus(response.data.status))
  }

  const logout = async () => {
    if (!error) {
      await axios.post('/logout').then(() => mutate())
    }

    window.location.pathname = '/login'
  }

  useEffect(() => {
    if (isLoading) return

    if (middleware === 'guest' && redirectIfAuthenticated && user) {
      router.push(redirectIfAuthenticated)
    }

    if (middleware === 'auth' && !user) {
      const attemptedPath = window.location.pathname
      const attemptedPathArray = attemptedPath.split('/')
    
      if (attemptedPathArray.includes('accept-invitation')) {
        setItem('callback_url', window.location.pathname)
      }
      
      router.push('/login')
    }

    if (middleware === 'auth' && !user?.email_verified_at)
      router.push('/verify-email')

    if (
      window.location.pathname === '/verify-email' &&
      user?.email_verified_at
    )
      router.push(redirectIfAuthenticated)

    if (middleware === 'auth' && error) logout()
  }, [user, error, isLoading])

  return {
    user,
    isLoading,
    register,
    login,
    forgotPassword,
    resetPassword,
    resendEmailVerification,
    logout,
  }
}
