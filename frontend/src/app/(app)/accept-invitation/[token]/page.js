'use client'

import { useStorage } from '@/hooks/storage';
import { useToast } from '@/hooks/toast';
import { acceptInvitation } from '@/lib/api';
import { useParams, useRouter } from 'next/navigation';
import { useEffect } from 'react';

const AcceptInvitation = () => {
  const { token } = useParams()
  const router = useRouter()
  const { showToast } = useToast()
  const { getItem } = useStorage('localStorage');
  const user = getItem('user');

  const fetchAcceptInvitation = async () => {
    try {
      const response = await acceptInvitation(user?.id, token)
      if (response.success) {
        showToast("Invitation accepted successfully!", "success")
        localStorage.removeItem('callback_url')
        router.push('/my-groups')
      }
    } catch (err) {
      showToast("Invitation accept error!", "error")
    }
  }

  useEffect(() => {
    token && fetchAcceptInvitation()
  }, [])

  return (
    <div>AcceptInvitation</div>
  )
}

export default AcceptInvitation