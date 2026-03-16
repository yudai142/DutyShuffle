/**
 * Data Process Worker
 * メインスレッドをブロックせずにバックグラウンドでデータ処理を実行
 * CPU集約的な配列操作、フィルタリング、変換などを処理
 */

// Worker内でのメッセージ受信
self.onmessage = function(event) {
  const { task, data, params } = event.data;
  
  try {
    let result = null;
    
    switch(task) {
      case 'format_allocation_list':
        result = formatAllocationList(data, params);
        break;
        
      case 'format_join_members':
        result = formatJoinMembers(data, params);
        break;
        
      case 'format_join_works':
        result = formatJoinWorks(data, params);
        break;
        
      case 'calculate_statistics':
        result = calculateStatistics(data, params);
        break;
        
      case 'batch_process_members':
        result = batchProcessMembers(data, params);
        break;
        
      case 'batch_process_works':
        result = batchProcessWorks(data, params);
        break;
        
      default:
        throw new Error(`Unknown task: ${task}`);
    }
    
    // メインスレッドに結果を送信
    self.postMessage({
      task: task,
      success: true,
      result: result,
      timestamp: Date.now()
    });
    
  } catch(error) {
    // エラーをメインスレッドに送信
    self.postMessage({
      task: task,
      success: false,
      error: error.message,
      stack: error.stack,
      timestamp: Date.now()
    });
  }
};

/**
 * 割り当てリストのフォーマット処理
 * @param {Array} data - API応答のデータ
 * @param {Object} params - 処理パラメータ
 * @returns {Array} フォーマット済みデータ
 */
function formatAllocationList(data, params = {}) {
  if (!Array.isArray(data) || data.length === 0) {
    return [];
  }
  
  // グループ化と集計
  const groupedByWork = {};
  const groupedByMember = {};
  
  data.forEach(item => {
    const workId = item.work_id;
    const memberId = item.member_id;
    
    if (!groupedByWork[workId]) {
      groupedByWork[workId] = [];
    }
    if (!groupedByMember[memberId]) {
      groupedByMember[memberId] = [];
    }
    
    groupedByWork[workId].push(item);
    groupedByMember[memberId].push(item);
  });
  
  // 処理済みデータ構造を構築
  return {
    raw: data,
    byWork: groupedByWork,
    byMember: groupedByMember,
    statistics: {
      totalAssignments: data.length,
      totalWorks: Object.keys(groupedByWork).length,
      totalMembers: Object.keys(groupedByMember).length,
      avgAssignmentsPerWork: data.length / Object.keys(groupedByWork).length,
      avgAssignmentsPerMember: data.length / Object.keys(groupedByMember).length
    }
  };
}

/**
 * プロフィール結合（メンバー）のフォーマット
 * @param {Array} data - API応答のデータ
 * @param {Object} params - 処理パラメータ
 * @returns {Object} フォーマット済みデータ
 */
function formatJoinMembers(data, params = {}) {
  if (!Array.isArray(data) || data.length === 0) {
    return { members: [], works: [] };
  }
  
  const members = [];
  const worksMap = {};
  
  data.forEach(item => {
    // メンバー情報を抽出（重複排除）
    if (item.member_id && !members.find(m => m.id === item.member_id)) {
      members.push({
        id: item.member_id,
        name: item.member_name,
        status: item.member_status,
        memo: item.member_memo
      });
    }
    
    // 作業情報を抽出
    if (item.work_id) {
      if (!worksMap[item.work_id]) {
        worksMap[item.work_id] = {
          id: item.work_id,
          name: item.work_name,
          assignedMembers: []
        };
      }
      worksMap[item.work_id].assignedMembers.push({
        id: item.member_id,
        name: item.member_name
      });
    }
  });
  
  const works = Object.values(worksMap);
  
  return {
    members: members,
    works: works,
    stats: {
      memberCount: members.length,
      workCount: works.length
    }
  };
}

/**
 * プロフィール結合（作業）のフォーマット
 * @param {Array} data - API応答のデータ
 * @param {Object} params - 処理パラメータ
 * @returns {Object} フォーマット済みデータ
 */
function formatJoinWorks(data, params = {}) {
  if (!Array.isArray(data) || data.length === 0) {
    return { works: [], members: [] };
  }
  
  const works = [];
  const membersMap = {};
  
  data.forEach(item => {
    // 作業情報を抽出（重複排除）
    if (item.work_id && !works.find(w => w.id === item.work_id)) {
      works.push({
        id: item.work_id,
        name: item.work_name,
        status: item.status || 'active',
        assignedMembers: []
      });
    }
    
    // メンバー情報を抽出
    if (item.member_id) {
      if (!membersMap[item.member_id]) {
        membersMap[item.member_id] = {
          id: item.member_id,
          name: item.member_name,
          assignedWorks: []
        };
      }
      membersMap[item.member_id].assignedWorks.push({
        id: item.work_id,
        name: item.work_name
      });
    }
    
    // 作業のメンバーリストに追加
    const work = works.find(w => w.id === item.work_id);
    if (work && work.assignedMembers.indexOf(item.member_id) === -1) {
      work.assignedMembers.push({
        id: item.member_id,
        name: item.member_name
      });
    }
  });
  
  const members = Object.values(membersMap);
  
  return {
    works: works,
    members: members,
    stats: {
      workCount: works.length,
      memberCount: members.length
    }
  };
}

/**
 * 統計情報の計算
 * @param {Array} data - 計算対象データ
 * @param {Object} params - 計算パラメータ
 * @returns {Object} 計算結果
 */
function calculateStatistics(data, params = {}) {
  if (!Array.isArray(data) || data.length === 0) {
    return {
      total: 0,
      unique: 0,
      average: 0,
      min: 0,
      max: 0
    };
  }
  
  const total = data.length;
  const unique = new Set(data.map(item => item.id || item)).size;
  const average = total / unique;
  
  // 数値フィールドの統計（あれば）
  const numericValues = data
    .map(item => item.count || item.value || 0)
    .filter(v => typeof v === 'number');
  
  const min = numericValues.length > 0 ? Math.min(...numericValues) : 0;
  const max = numericValues.length > 0 ? Math.max(...numericValues) : 0;
  
  return {
    total: total,
    unique: unique,
    average: parseFloat((average).toFixed(2)),
    min: min,
    max: max,
    timestamp: Date.now()
  };
}

/**
 * バッチ処理（メンバー）
 * @param {Object} data - 複数のメンバー関連データ
 * @param {Object} params - 処理パラメータ
 * @returns {Object} 処理結果
 */
function batchProcessMembers(data, params = {}) {
  const { members = [], works = [], assignments = [] } = data;
  
  // メンバーデータの整形
  const processedMembers = members.map(member => ({
    ...member,
    assignmentCount: assignments.filter(a => a.member_id === member.id).length,
    workIds: assignments
      .filter(a => a.member_id === member.id)
      .map(a => a.work_id)
  }));
  
  // メンバーのソート
  const sorted = processedMembers.sort((a, b) => 
    b.assignmentCount - a.assignmentCount
  );
  
  return {
    members: sorted,
    totalMembers: sorted.length,
    averageAssignments: sorted.length > 0 
      ? (assignments.length / sorted.length).toFixed(2)
      : 0,
    summary: {
      maxAssignments: Math.max(...sorted.map(m => m.assignmentCount), 0),
      minAssignments: Math.min(...sorted.map(m => m.assignmentCount), 0)
    }
  };
}

/**
 * バッチ処理（作業）
 * @param {Object} data - 複数の作業関連データ
 * @param {Object} params - 処理パラメータ
 * @returns {Object} 処理結果
 */
function batchProcessWorks(data, params = {}) {
  const { works = [], members = [], assignments = [] } = data;
  
  // 作業データの整形
  const processedWorks = works.map(work => ({
    ...work,
    assignmentCount: assignments.filter(a => a.work_id === work.id).length,
    memberIds: assignments
      .filter(a => a.work_id === work.id)
      .map(a => a.member_id)
  }));
  
  // 作業のソート
  const sorted = processedWorks.sort((a, b) => 
    b.assignmentCount - a.assignmentCount
  );
  
  return {
    works: sorted,
    totalWorks: sorted.length,
    averageAssignments: sorted.length > 0 
      ? (assignments.length / sorted.length).toFixed(2)
      : 0,
    summary: {
      maxAssignments: Math.max(...sorted.map(w => w.assignmentCount), 0),
      minAssignments: Math.min(...sorted.map(w => w.assignmentCount), 0),
      totalAssignments: assignments.length
    }
  };
}
