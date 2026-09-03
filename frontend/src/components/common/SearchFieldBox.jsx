import { Box, IconButton } from '@mui/material'
import React, { useState } from 'react'
import TextFieldCommon from './TextFieldCommon'
import { IoSearch } from "react-icons/io5";
import { useToast } from '@/hooks/toast';

const SearchFieldBox = ({apiFunc, setSearchResult}) => {
  const [params, setParams] = useState('')
  const { showToast } = useToast()

  const submitSearch = async (e) => {
    e.preventDefault()
    
    try {
      const searchRes = await apiFunc(params)
      if (searchRes?.success) {
        setSearchResult(searchRes?.data)
      }
    } catch (err) {
      showToast("Search failed!", "error")
    }
  }

  return (
    <Box
      component="form"
      onSubmit={submitSearch}
      sx={{
        display: "flex",
        alignItems: "center",
        justifyContent: { xs: "center", md: "right" },
        gap: 1,
      }}
    >
      <Box>
        <TextFieldCommon
          id="search"
          name="search"
          type="text"
          placeholder="Search here..."
          value={params}
          onchangeInputValue={(e) => {setParams(e.target.value)}}
        />
      </Box>
      <Box>
        <IconButton size="small" sx={{bgcolor: '#ccc'}} type='submit'>
          <IoSearch />
        </IconButton>
      </Box>
    </Box>
  )
}

export default SearchFieldBox