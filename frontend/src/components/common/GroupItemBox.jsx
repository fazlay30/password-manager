'use client'

import React from 'react'
import { Box, Typography } from "@mui/material"
import Link from 'next/link'

const GroupItemBox = ({groupItem}) => {

  return (
    <Link href={`/my-groups/${groupItem.id}`}>
      <Box
        sx={{
          border: "1px solid",
          borderColor: "grey.300",
          borderRadius: 2,
          padding: 3,
          bgcolor: "background.paper",
          boxShadow: 1,
        }}
      >
        <Typography variant="h6" color="text.primary" mb={1}>
          {groupItem?.name}
        </Typography>
        <Typography variant="body2" color="text.secondary" mb={1}>
          {groupItem?.description}
        </Typography>
        <Typography variant="body2" color="text.secondary" mb={1}>
          My Role: {groupItem?.user_role}
        </Typography>
      </Box>
    </Link>
  )
}

export default GroupItemBox