'use client'

import Button from '@/components/Button'
import Input from '@/components/Input'
import InputError from '@/components/InputError'
import Label from '@/components/Label'
import Link from 'next/link'
import { useAuth } from '@/hooks/auth'
import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import AuthSessionStatus from '@/app/(auth)/AuthSessionStatus'
import { Stack } from '@mui/material'

const Login = () => {
  const [acceptInvitationToken, setAcceptInvitationToken] = useState('')
  const router = useRouter()

  const { login } = useAuth({
    middleware: 'guest',
    redirectIfAuthenticated: '/dashboard',
  })

  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [shouldRemember, setShouldRemember] = useState(false)
  const [errors, setErrors] = useState([])
  const [status, setStatus] = useState(null)

  useEffect(() => {
    if (router.reset?.length > 0 && errors.length === 0) {
      setStatus(atob(router.reset))
    } else {
      setStatus(null)
    }
  })

  // check this is from accept invitaion
  useEffect(() => {
    if (typeof window !== 'undefined') {
      const savedCallbackUrl = localStorage.getItem('callback_url')
      if (savedCallbackUrl) {
        const sanitizedUrl = savedCallbackUrl?.replace(/["]+/g, '')

        const callbackUrlItems = sanitizedUrl?.split('/')

        if (callbackUrlItems.includes('accept-invitation')) {
          setAcceptInvitationToken(callbackUrlItems?.[2])
        }
      }
    }
  }, [])

  const submitForm = async event => {
    event.preventDefault()

    login({
      email,
      password,
      remember: shouldRemember,
      setErrors,
      setStatus,
    })
  }

  return (
    <>
      <AuthSessionStatus className="mb-4" status={status} />
      <form onSubmit={submitForm}>
        {/* Email Address */}
        <div>
          <Label htmlFor="email">Email</Label>

          <Input
            id="email"
            type="email"
            value={email}
            className="block mt-1 w-full"
            onChange={event => setEmail(event.target.value)}
            required
            autoFocus
          />

          <InputError messages={errors.email} className="mt-2" />
        </div>

        {/* Password */}
        <div className="mt-4">
          <Label htmlFor="password">Password</Label>

          <Input
            id="password"
            type="password"
            value={password}
            className="block mt-1 w-full"
            onChange={event => setPassword(event.target.value)}
            required
            autoComplete="current-password"
          />

          <InputError
            messages={errors.password}
            className="mt-2"
          />
        </div>

        {/* Remember Me */}
        <div className="flex items-center justify-between mt-4">
          <label
            htmlFor="remember_me"
            className="inline-flex items-center">
            <input
              id="remember_me"
              type="checkbox"
              name="remember"
              className="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              onChange={event =>
                setShouldRemember(event.target.checked)
              }
            />

            <span className="ml-2 text-sm text-gray-600">
              Remember me
            </span>
          </label>
          <Link
            href="/forgot-password"
            className="underline text-sm text-gray-600 hover:text-gray-900">
              Forgot your password?
          </Link>
        </div>

        <div className="mt-4">
          <Button className="w-full bg-blue-600">Login</Button>
        </div>

        <div className="flex items-center justify-center mt-4 gap-3">
          <h3>OR</h3>
          <Link
            href={
              acceptInvitationToken !== '' ? `${process.env.NEXT_PUBLIC_BACKEND_URL}/auth/google/redirect?group-invite=true&token=${acceptInvitationToken}`
                : `${process.env.NEXT_PUBLIC_BACKEND_URL}/auth/google/redirect`
            }
          >
            <Stack
              spacing={{ xs: 1, sm: 2 }}
              direction="row"
              useFlexGap
              sx={{ flexWrap: 'wrap', alignItems: 'center', justifyContent: 'center' }}
            >
              <div>
                <img
                  src={'/images/google-logo.png'}
                  alt="logo"
                  className='mx-auto w-[20px] h-[20px]'
                />
              </div>
              <div>Sign in using Google</div>
            </Stack>
          </Link>
        </div>
        <div className='mt-2'>
          <p className='text-red-800 text-center'>
            <Link href={'/register'}>Not registered yet? register now.</Link>
          </p>
        </div>
      </form>
    </>
  )
}

export default Login
